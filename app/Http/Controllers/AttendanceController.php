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
    public function showTrainerScanner()
    {
        return view('trainerDashboard.qr');
    }

    public function showMemberScanner()
    {
        return view('memberDashboard.qr');
    }

    public function showQR()
    {
        $date  = Carbon::now()->toDateString();
        $token = Crypt::encryptString($date);
        $url   = 'https://geethikaworks.com/checkin-token?token=' . $token;

        $qrCode = QrCode::size(250)->generate($url);

        return view('adminDashboard.qr_display', compact('qrCode', 'token', 'url'));
    }

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

    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat        = deg2rad($lat2 - $lat1);
        $dLon        = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
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

    public function viewAll(Request $request)
    {
        $query = Attendance::with(['user.userRole.role'])->latest();

        if ($request->filled('date')) {
            $query->whereDate('check_in_time', $request->input('date'));
        }

        if ($request->filled('member_id')) {
            $query->where('user_id', $request->input('member_id'));
        }

        if ($request->filled('role')) {
            $role = $request->input('role');
            $query->whereHas('user.userRole.role', function ($q) use ($role) {
                $q->where('role_name', $role);
            });
        }

        $attendances = $query->paginate(15)->appends($request->all());

        $members = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->join('roles', 'user_roles.role_id', '=', 'roles.role_id')
            ->select('users.id', 'users.first_name', 'users.last_name', 'roles.role_name')
            ->get();

        return view('adminDashboard.attendance', compact('attendances', 'members'));
    }

    public function storeManual(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'check_in_time' => 'required|date',
            'status'        => 'required|in:Present,Absent',
        ]);

        Attendance::create([
            'user_id'           => $request->user_id,
            'check_in_time'     => $request->check_in_time,
            'status'            => $request->status,
            'qr_code'           => null,
            'token_valid_until' => null,
        ]);

        return back()->with('success', 'Manual attendance recorded.');
    }

    public function searchUsers(Request $request)
    {
        $query = $request->input('q');

        $results = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->join('roles', 'user_roles.role_id', '=', 'roles.role_id')
            ->select('users.id', 'users.first_name', 'users.last_name', 'roles.role_name')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('users.first_name', 'like', '%' . $query . '%')
                        ->orWhere('users.last_name', 'like', '%' . $query . '%');
                });
            })
            ->limit(10)
            ->get();

        return response()->json($results);
    }

}
