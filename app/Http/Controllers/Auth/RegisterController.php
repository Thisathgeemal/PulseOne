<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    // show registration form
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // // handle registration step one
    // public function registerStepOne(Request $request)
    // {
    //     $validated = $request->validate([
    //         'first_name'      => 'required|string|max:255',
    //         'last_name'       => 'required|string|max:255',
    //         'email'           => 'required|string|email|max:255|unique:users,email',
    //         'password'        => 'required|string|min:8',
    //         'contact_number'  => 'required|string|max:20',
    //         'membership_type' => 'required|string|max:20',
    //     ]);

    //     session([
    //         'registration_data' => $validated,
    //         'show_payment'      => true,
    //     ]);

    //     return redirect()->route('register')->withInput();
    // }

    // // handle registration step two
    // public function registerStepTwo(Request $request)
    // {
    //     if (! session('show_payment') || ! session('registration_data')) {
    //         abort(403, 'Please complete registration first.');
    //     }

    //     $request->validate([
    //         'card_type'    => 'required|string',
    //         'card_name'    => 'required|string|max:255',
    //         'card_number'  => 'required|string|max:16',
    //         'cvv'          => 'required|string|max:3',
    //         'expiry_month' => 'required|string',
    //         'expiry_year'  => 'required|string',
    //     ]);

    //     $registrationData = session('registration_data');

    //     $user = User::create([
    //         'first_name'      => $registrationData['first_name'],
    //         'last_name'       => $registrationData['last_name'],
    //         'email'           => $registrationData['email'],
    //         'password'        => Hash::make($registrationData['password']),
    //         'contact_number'  => $registrationData['contact_number'],
    //         'membership_type' => $registrationData['membership_type'],
    //     ]);

    //     Auth::login($user);

    //     session()->forget(['registration_data', 'show_payment']);

    //     return redirect()->route('home');
    // }
}
