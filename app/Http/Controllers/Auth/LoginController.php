<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
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
    public function register(Request $request)
    {
        $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'required|string|email|max:255|unique:users',
            'contact_number'  => 'required|string|max:20',
            'membership_type' => 'required|string|max:20',
            'password'        => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'first_name'      => $request->first_name,
            'last_name'       => $request->last_name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'contact_number'  => $request->contact_number,
            'membership_type' => $request->membership_type,
        ]);

        Auth::login($user);

        return redirect()->route('home');
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
