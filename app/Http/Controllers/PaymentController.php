<?php
namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    // display payment details for admin
    public function getPaymentData(Request $request)
    {
        $search = $request->input('search');
        $date   = $request->input('date');

        $payments = Payment::with(['user', 'membershipType'])
            ->when($date, function ($query) use ($date) {
                $query->whereDate('payment_date', $date);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($q2) use ($search) {
                        $q2->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%");
                    })
                        ->orWhere('payment_method', 'like', "%$search%")
                        ->orWhereHas('membershipType', function ($q3) use ($search) {
                            $q3->where('type_name', 'like', "%$search%");
                        });
                });
            })
            ->orderBy('payment_date', 'desc')
            ->paginate(7);

        return view('adminDashboard.payment', compact('payments'));
    }

    // display payment details for relevant member
    public function getMemberPaymentData(Request $request)
    {
        $search = $request->input('search');
        $date   = $request->input('date');
        $userId = Auth::id();

        $payments = Payment::with(['membershipType'])
            ->where('user_id', $userId)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('payment_date', $date);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('payment_method', 'like', "%$search%")
                        ->orWhereHas('membershipType', function ($subQ) use ($search) {
                            $subQ->where('type_name', 'like', "%$search%");
                        });
                });
            })
            ->orderBy('payment_date', 'desc')
            ->paginate(7);

        return view('memberDashboard.payment', compact('payments'));
    }

}
