<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // handle login request
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $role = $user->roles();

            if ($role->count() > 1) {
                session(['available_roles' => $roles]);
                return redirect()->route('select-role');
            } else {
                $role = $role->first();
                return $this->redirectToDashboard($role->name);
            }
        }

        return back()->withErrors([
            'email' => 'Invalid credentials',
        ]);
    }

    // show role selection form
    public function showRoleSelection()
    {
        $roles = session('available_roles');
        return view('auth.selectRole', compact('roles'));
    }

    // handle role selection
    public function selectRole(Request $request)
    {
        $roleId = $request->input('role_id');
        $role   = Role::find($roleId);

        return $this->redirectToDashboard($role->name);
    }

    // redirect to dashboard based on role
    protected function redirectToDashboard($roleName)
    {
        switch ($roleName) {
            case 'Admin':
                return redirect()->route('admin.dashboard');
            case 'Trainer':
                return redirect()->route('trainer.dashboard');
            case 'Dietitian':
                return redirect()->route('dietitian.dashboard');
            case 'Member':
                return redirect()->route('member.dashboard');
            default:
                return redirect('/');
        }
    }

    // show registration form
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // handle registration request

    // handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
