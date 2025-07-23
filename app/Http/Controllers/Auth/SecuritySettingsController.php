<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SecuritySettingsController extends Controller
{
    // Log out a specific device
    public function logoutDevice(Request $request)
    {
        DB::table('sessions')
            ->where('user_id', Auth::id())
            ->where('id', $request->session_id)
            ->delete();

        return back()->with('success', 'Device logged out successfully.');
    }

    // Log out all devices except the current one
    public function logoutAllDevices()
    {
        DB::table('sessions')
            ->where('user_id', Auth::id())
            ->where('id', '!=', Session::getId())
            ->delete();

        return back()->with('success', 'All other devices have been logged out.');
    }

    // MFA enable or disable
    public function toggleMfa(Request $request)
    {
        $user              = Auth::user();
        $user->mfa_enabled = ! $user->mfa_enabled;
        $user->save();

        $statusMessage = $user->mfa_enabled
        ? 'Two-factor authentication has been enabled.'
        : 'Two-factor authentication has been disabled.';

        return redirect()->back()->with('success', $statusMessage);
    }
}
