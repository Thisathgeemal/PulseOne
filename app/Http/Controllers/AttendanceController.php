<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Show QR Scanner page with member's attendance history
     */
    public function showMemberQR()
    {
        $user = Auth::user();

        // ✅ Check if logged in and has 'Member' role
        if (!$user || !$user->hasRole('Member')) {
            abort(403, 'Unauthorized');
        }

        // ✅ Fetch attendance records for the logged-in member
        $attendances = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        return view('memberDashboard.qrscanner', compact('attendances'));
    }

    /**
     * Mark attendance from dashboard (POST from "Scan" button)
     */
    public function markAttendance(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole('Member')) {
            abort(403, 'Unauthorized');
        }

        $today = Carbon::today()->toDateString();

        // Prevent multiple entries on the same day
        if (Attendance::where('user_id', $user->id)->where('date', $today)->exists()) {
            return redirect()->back()->with('info', 'You have already marked attendance today.');
        }

        Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'time_in' => Carbon::now()->toTimeString(),
        ]);

        return redirect()->back()->with('success', 'Attendance marked successfully!');
    }

    /**
     * Mark attendance via QR code scan (GET request from scanned QR)
     */
    public function markAttendanceViaQR()
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole('Member')) {
            abort(403, 'Unauthorized');
        }

        $today = Carbon::today()->toDateString();

        // Prevent multiple entries on the same day
        if (Attendance::where('user_id', $user->id)->where('date', $today)->exists()) {
            return redirect()->route('member.qrscanner')->with('info', 'You have already marked attendance today.');
        }

        Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'time_in' => Carbon::now()->toTimeString(),
        ]);

        return redirect()->route('member.qrscanner')->with('success', 'Attendance marked via QR!');
    }
}
