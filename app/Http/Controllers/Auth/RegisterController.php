<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\MembershipConfirmationMail;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{
    // show registration form
    public function showRegistrationForm()
    {
        $showPayment    = session()->has('member_data');
        $membershipType = MembershipType::all();
        return view('auth.register', compact('membershipType', 'showPayment'));
    }

    // Store member details in session
    public function registerMember(Request $request)
    {
        try {
            $data = $request->validate([
                'first_name'      => 'required|string|max:255',
                'last_name'       => 'required|string|max:255',
                'email'           => 'required|string|email|max:255',
                'password'        => 'required|string|min:6',
                'contact_number'  => [
                    'required',
                    'regex:/^\+?[0-9\s\-\(\)]+$/',
                ],
                'membership_type' => 'required|exists:membership_types,type_id',
                'price'           => 'required|numeric',
            ]);

            $user = User::where('email', $data['email'])->first();

            if ($user) {
                $hasMemberRole = $user->roles()->where('role_name', 'Member')->exists();

                if ($hasMemberRole) {
                    return redirect()->back()
                        ->withErrors(['email' => 'This email is already registered as a member.'])
                        ->withInput();
                }
            }

            $data['password'] = Hash::make($data['password']);

            if (! session()->has('member_data')) {
                session(['member_data' => $data]);
            }

            return redirect()->route('register')->with('showPayment', true);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    // Handle payment registration
    public function registerPayment(Request $request)
    {
        try {
            $data = $request->validate([
                'card_type'    => 'required|string',
                'card_name'    => 'required|string',
                'card_number'  => 'required|string|size:16',
                'expiry_month' => 'required|between:1,12',
                'expiry_year'  => 'required|digits:4',
                'cvv'          => 'required|string|size:3',
            ]);

            $memberData = session('member_data');
            if (! $memberData) {
                return redirect()->route('register')->withErrors(['session' => 'Member data session expired. Please register again.']);
            }

            $memberSelectType = MembershipType::find($memberData['membership_type']);
            if (! $memberSelectType) {
                return redirect()->route('register')->withErrors(['membership' => 'Invalid membership type selected.']);
            }

            $startDate = now();
            $endDate   = $startDate->copy()->addDays($memberSelectType->duration);
            $method    = 'Card';

            DB::beginTransaction();

            $user = User::where('email', $memberData['email'])->first();

            if (! $user) {
                $user = User::create([
                    'email'         => $memberData['email'],
                    'password'      => $memberData['password'],
                    'first_name'    => $memberData['first_name'],
                    'last_name'     => $memberData['last_name'],
                    'mobile_number' => $memberData['contact_number'],
                ]);

                if (! $user) {
                    throw new \Exception('Failed to create user.');
                }
            }

            $memberRole = Role::where('role_name', 'Member')->first();
            if (! $memberRole) {
                throw new \Exception('Member role not found in the system.');
            }

            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $memberRole->role_id,
            ]);

            Membership::create([
                'user_id'    => $user->id,
                'type_id'    => $memberData['membership_type'],
                'start_date' => $startDate,
                'end_date'   => $endDate,
            ]);

            Payment::create([
                'user_id'        => $user->id,
                'type_id'        => $memberData['membership_type'],
                'payment_method' => $method,
                'amount'         => $memberData['price'],
            ]);

            Mail::to($user->email)->send(new MembershipConfirmationMail($user, $memberSelectType));

            DB::commit();

            session()->forget('member_data');
            return redirect()->route('register')->with('showSuccess', true);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->forget('member_data');

            return redirect()->route('register')->withErrors([
                'registration' => 'Registration failed: ' . $e->getMessage(),
            ]);
        }
    }

}
