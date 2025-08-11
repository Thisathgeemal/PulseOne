<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    // show login form
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();

            $roles     = UserRole::where('user_id', $user->id)->pluck('role_id');
            $roleNames = Role::whereIn('role_id', $roles)->pluck('role_name')->toArray();

            session(['user_roles' => $roleNames]);

            return $this->handleRoleRedirect($roleNames);
        }
        return view('auth.login');
    }

    // handle login request
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);
        $remember = $request->filled('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
        }

        $user = Auth::user();

        if ($user->is_active !== true) {
            Auth::logout();
            return back()->withErrors(['email' => 'Your account is inactive.'])->onlyInput('email');
        }

        $activeRoleNames = UserRole::where('user_id', $user->id)
            ->where('is_active', true)
            ->join('roles', 'user_roles.role_id', '=', 'roles.role_id')
            ->pluck('roles.role_name')
            ->toArray();

        $request->session()->regenerate();

        session(['user_roles' => $activeRoleNames]);

        if ($user->mfa_enabled) {
            $code      = rand(100000, 999999);
            $expiresAt = now()->addMinutes(3);
            session([
                '2fa_code'       => $code,
                '2fa_expires_at' => $expiresAt,
            ]);

            Mail::send('emails.2faCode', ['user' => $user, 'code' => $code], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Your 2FA Verification Code');
            });

            return redirect()->route('2fa.verify');
        }

        return $this->handleRoleRedirect($activeRoleNames);
    }

    // handle 2FA verification
    public function verify2FA(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $code      = session('2fa_code');
        $expiresAt = session('2fa_expires_at');

        if (now()->greaterThan($expiresAt)) {
            return back()->withErrors(['code' => 'Verification code has expired.']);
        }

        if ($request->code != $code) {
            return back()->withErrors(['code' => 'Invalid verification code.']);
        }

        $roles = session('user_roles');
        return $this->handleRoleRedirect($roles);
    }

    // handle one role selection
    protected function handleRoleRedirect(array $roles)
    {
        if (count($roles) == 1) {
            session(['active_role' => $roles[0]]);
            return redirect()->route($roles[0] . '.dashboard');
        }

        return redirect()->route('selectRole');
    }

    // handle role selection form
    public function submitSelectedRole(Request $request)
    {
        $request->validate([
            'selected_role' => ['required', 'string'],
        ]);

        $selectedRole = $request->input('selected_role');
        $user         = Auth::user();

        $isRoleStillActive = DB::table('user_roles')
            ->join('roles', 'user_roles.role_id', '=', 'roles.role_id')
            ->where('user_roles.user_id', $user->id)
            ->where('roles.role_name', $selectedRole)
            ->where('user_roles.is_active', true)
            ->exists();

        if (! $isRoleStillActive) {
            return back()->with(['error' => 'Selected role is not active.']);
        }

        session(['active_role' => $selectedRole]);
        return redirect()->route($selectedRole . '.dashboard');
    }

    // resend the 2FA code
    public function resend2FA(Request $request)
    {
        $user = Auth::user();

        if (! $user || ! $user->mfa_enabled) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired or MFA not enabled.']);
        }

        $code      = rand(100000, 999999);
        $expiresAt = now()->addMinutes(3);

        session([
            '2fa_code'       => $code,
            '2fa_expires_at' => $expiresAt,
        ]);

        Mail::send('emails.2faNewCode', ['user' => $user, 'code' => $code], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Your New 2FA Verification Code');
        });

        return back()->with('status', 'A new verification code has been sent to your email.');
    }

    // show forget password email enter form
    public function showForgotPasswordForm()
    {
        return view('auth.forgotPassword');
    }

    // handles sending the password reset link
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
    }

    // show password reset from
    public function showResetForm($token)
    {
        return view('auth.resetPassword', ['token' => $token]);
    }

    // handles resetting a user's password
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return back()->with('reset_success', true);
        }

        return back()->with('reset_error', __($status));
    }

    // handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
