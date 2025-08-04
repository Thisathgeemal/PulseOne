<?php
namespace App\Http\Controllers;

use App\Helpers\QrTokenHelper;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceController extends Controller
{
    // Show the QR scanner page for Trainers
    public function showTrainerScanner()
    {
        return view('trainerDashboard.qr');
    }

    // Show the QR scanner page for Members
    public function showMemberScanner()
    {
        return view('memberDashboard.qr');
    }

    // Generate and display a QR code for today's check-in (admin only)
    public function showQR()
    {
        $date  = Carbon::now()->toDateString();
        $token = Crypt::encryptString($date);
        $url   = 'https://geethikaworks.com/checkin-token?token=' . $token;

        $qrCode = QrCode::size(250)->generate($url);

        return view('adminDashboard.qr_display', compact('qrCode', 'token', 'url'));
    }

    // Handle token redirect after QR code scan and route users to their dashboard
    public function handleTokenRedirect(Request $request)
    {
        $token = $request->query('token');

        try {
            $decrypted = Crypt::decryptString($token);
            $today     = now()->toDateString();

            if (! auth()->check()) {
                return redirect()->route('login');
            }

            // Get user role
            $role          = auth()->user()->userRole->role->role_name;
            $redirectRoute = $role === 'Trainer' ? 'trainer.qr' : 'member.qr';

            if ($decrypted === $today) {
                return redirect()->route($redirectRoute)->with('checkin_token', $token);
            } else {
                return redirect()->route($redirectRoute)->with('error', 'QR code expired.');
            }
        } catch (\Exception $e) {
            return redirect()->route('member.qr')->with('error', 'Invalid QR code.');
        }
    }

    // Handle the actual check-in process
    public function checkin(Request $request)
    {
        $request->validate([
            'qr_code'   => 'required',
            'latitude'  => 'nullable',
            'longitude' => 'nullable',
        ]);

        try {
            $user = Auth::user();

            if (! $user->is_active) {
                return back()->with('error', 'Your account is deactivated. Please contact the admin.');
            }

            if (! QrTokenHelper::validateToken($request->qr_code)) {
                return back()->with('error', 'Invalid or expired QR code.');
            }

            $existing = Attendance::where('user_id', $user->id)
                ->whereDate('created_at', now()->toDateString())
                ->first();

            if ($existing) {
                return back()->with('error', 'You have already checked in today.');
            }

            $role = $user->userRole->role->role_name;

            if ($role === 'Trainer') {
                $gymLat = 7.124814;
                $gymLng = 80.568809;

                $userLat = $request->latitude;
                $userLng = $request->longitude;

                if (! $userLat || ! $userLng) {
                    return back()->with('error', 'Location access required for trainer check-in.');
                }

                $distance = $this->haversine($gymLat, $gymLng, $userLat, $userLng);
                \Log::info("🔍 Trainer check-in distance: $distance km");

                if ($distance > 0.005) {
                    return back()->with('error', 'You are not near the gym area.');
                }
            }

            $ip = $request->ip();

            Attendance::create([
                'user_id'           => $user->id,
                'check_in_time'     => now(),
                'status'            => 'Present',
                'qr_code'           => $request->qr_code,
                'ip_address'        => $ip,
                'latitude'          => $request->latitude,
                'longitude'         => $request->longitude,
                'token_valid_until' => now()->endOfDay(),
            ]);

            return back()->with('success', 'Check-in successful!');
        } catch (\Exception $e) {
            \Log::error('Check-in failed: ' . $e->getMessage());
            return back()->with('error', 'An error occurred during check-in.');
        }
    }

    // Member Attendance
    public function viewMemberAttendance(Request $request)
    {
        $user = Auth::user();
        $date = $request->input('date');

        $query = Attendance::where('user_id', $user->id);

        if ($date) {
            $query->whereDate('check_in_time', $date);
        }

        $attendances = $query->latest()->paginate(10);

        return view('memberDashboard.attendance', compact('attendances', 'date'));
    }

    // Trainer Attendance
    public function viewTrainerAttendance(Request $request)
    {
        $user = Auth::user();
        $date = $request->input('date');

        $query = Attendance::where('user_id', $user->id);

        if ($date) {
            $query->whereDate('check_in_time', $date);
        }

        $attendances = $query->latest()->paginate(10);

        return view('trainerDashboard.attendance', compact('attendances', 'date'));
    }

    // View all attendance records with filters
    public function viewAll(Request $request)
    {
        $query = Attendance::with(['user.roles'])->latest();

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('check_in_time', $request->input('date'));
        }

        // Filter by member ID
        if ($request->filled('member_id')) {
            $query->where('user_id', $request->input('member_id'));
        }

        // Filter by role name (supports users with multiple roles)
        if ($request->filled('role')) {
            $role = $request->input('role');
            $query->whereHas('user.roles', function ($q) use ($role) {
                $q->where('role_name', $role);
            });
        } else {
            // Only include attendances of users with role Member or Trainer
            $query->whereHas('user.roles', function ($q) {
                $q->whereIn('role_name', ['Member', 'Trainer']);
            });
        }

        // Paginate and retain filters
        $attendances = $query->paginate(10)->appends($request->all());

        // Get only users with Member or Trainer roles
        $members = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->join('roles', 'user_roles.role_id', '=', 'roles.role_id')
            ->whereIn('roles.role_name', ['Member', 'Trainer'])
            ->select('users.id', 'users.first_name', 'users.last_name', 'roles.role_name')
            ->get();

        return view('adminDashboard.attendance', compact('attendances', 'members'));
    }

    // Manually store attendance for a user
    public function storeManual(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date'    => 'required|date',
            'time'    => 'required|date_format:H:i',
        ]);

        $checkInDateTime = $request->date . ' ' . $request->time . ':00';

        Attendance::create([
            'user_id'           => $request->user_id,
            'check_in_time'     => $checkInDateTime,
            'qr_code'           => null,
            'token_valid_until' => null,
        ]);

        return back()->with('success', 'Manual attendance recorded.');
    }

    // Attendance checkout
    public function checkout($id)
    {
        $attendance = Attendance::findOrFail($id);

        if (! $attendance->check_out_time) {
            $attendance->check_out_time = now();
            $attendance->save();
        }

        return redirect()->back()->with('success', 'Checked out successfully!');
    }

    // Search members
    public function searchUsers(Request $request)
    {
        $query = $request->input('q');

        $users = User::where(function ($q) use ($query) {
            $q->where('first_name', 'like', "%$query%")
                ->orWhere('last_name', 'like', "%$query%");
        })
            ->whereHas('roles', function ($q) {
                $q->whereIn('role_name', ['Member', 'Trainer']);
            })
            ->with('roles')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id'         => $user->id,
                    'first_name' => $user->first_name,
                    'last_name'  => $user->last_name,
                    'role_name'  => $user->roles->first()->role_name ?? '',
                ];
            });

        return response()->json($users);
    }

}
