<?php
namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /* ========= MEMBER ========= */

    // List my submitted feedback
    public function memberIndex()
    {
        $items = Feedback::with('toUser')
            ->where('from_user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);

        // For "New Feedback" modal / page
        $trainers   = User::whereHas('roles', fn($q) => $q->where('role_name', 'Trainer'))->orderBy('first_name')->get();
        $dietitians = User::whereHas('roles', fn($q) => $q->where('role_name', 'Dietitian'))->orderBy('first_name')->get();

        return view('memberDashboard.feedback', compact('items', 'trainers', 'dietitians'));
    }

    // Show create form
    public function create()
    {
        $trainers   = User::whereHas('roles', fn($q) => $q->where('role_name', 'Trainer'))->orderBy('first_name')->get();
        $dietitians = User::whereHas('roles', fn($q) => $q->where('role_name', 'Dietitian'))->orderBy('first_name')->get();

        return view('memberDashboard.feedbackCreate', compact('trainers', 'dietitians'));
    }

    // Save new feedback
    public function store(Request $request)
    {
        $data = $request->validate([
            'type'       => 'required|in:Trainer,Dietitian,System',
            'to_user_id' => 'nullable|exists:users,id',
            'rate'       => 'required|integer|min:1|max:5',
            'content'    => 'required|string|max:3000',
        ]);

        if (in_array($data['type'], ['Trainer', 'Dietitian'])) {
            $request->validate(['to_user_id' => 'required|exists:users,id']);
        } else {
            $data['to_user_id'] = null;
        }

        $data['from_user_id'] = auth()->id();
        $data['is_visible']   = true;
        $data['created_at']   = now();

        \App\Models\Feedback::create($data);

        return redirect()
            ->route('member.feedback')
            ->with('success', 'Thanks! Your feedback was submitted.');
    }

    /* ========= TRAINER ========= */

    public function trainerIndex()
    {
        $items = Feedback::with('fromUser')
            ->where('type', 'Trainer')
            ->where('to_user_id', Auth::id())
            ->where('is_visible', true)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('trainerDashboard.feedback', compact('items'));
    }

    /* ========= DIETITIAN ========= */

    public function dietitianIndex()
    {
        $items = Feedback::with('fromUser')
            ->where('type', 'Dietitian')
            ->where('to_user_id', Auth::id())
            ->where('is_visible', true)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('dietitianDashboard.feedback', compact('items'));
    }

    /* ========= ADMIN ========= */

    public function adminIndex(Request $request)
    {
        $items = Feedback::with(['fromUser', 'toUser'])
            ->when($request->type, fn($q, $v) => $q->where('type', $v))
            ->when($request->visibility !== null && $request->visibility !== '', fn($q, $v) => $q->where('is_visible', (bool) $v))
            ->when($request->rate, fn($q, $v) => $q->where('rate', $v))
            ->when($request->q, fn($q, $v) => $q->where('content', 'like', "%$v%"))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('adminDashboard.feedback', compact('items'));
    }

    public function adminToggleVisibility($id, Request $request)
    {
        $request->validate(['is_visible' => 'required|boolean']);
        Feedback::whereKey($id)->update(['is_visible' => (bool) $request->is_visible]);
        return back()->with('success', 'Visibility updated.');
    }
}
