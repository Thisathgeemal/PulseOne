<?php
namespace App\Http\Controllers;

use App\Mail\MembershipCancelledMail;
use App\Mail\MembershipConfirmationMail;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MembershipController extends Controller
{
    // display membership details
    public function getmembershipData(Request $request)
    {
        $search    = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        $memberships = Membership::with(['user', 'membershipType'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    })->orWhereHas('membershipType', function ($typeQuery) use ($search) {
                        $typeQuery->where('type_name', 'like', "%{$search}%");
                    })->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate]);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('end_date', [$startDate, $endDate]);
            })
            ->orderByDesc('created_at')
            ->paginate(5);

        $membershipType = MembershipType::all();

        return view('adminDashboard.membership', compact('memberships', 'membershipType'));
    }

    // create membership
    public function createMembership(Request $request)
    {
        try {
            $request->validate([
                'member_id'        => 'nullable|exists:users,id',
                'member_name'      => 'nullable|string',
                'membership_type'  => 'required|exists:membership_types,type_id',
                'membership_price' => 'required|numeric',
            ]);

            $member = null;

            if ($request->filled('member_id')) {
                $member = User::find($request->member_id);
            } elseif ($request->filled('member_name')) {
                $member = User::where('first_name', $request->member_name)->first();

                if (! $member) {
                    return back()->with('error', 'No user found with the given name.');
                }
            } else {
                return back()->with('error', 'Please provide a Member ID or Member Name.');
            }

            // Check role
            $hasMemberRole = $member->roles()->where('role_name', 'Member')->exists();
            if (! $hasMemberRole) {
                return back()->with('error', 'The selected user is not assigned the Member role.');
            }

            $membershipType = MembershipType::find($request->membership_type);
            if (! $membershipType) {
                return back()->with('error', 'Invalid membership type selected.');
            }

            $pendingMembership = Membership::where('user_id', $member->id)
                ->where('status', 'Pending')
                ->first();

            if ($pendingMembership) {
                return back()->with('error', 'You already have a pending membership. Please wait until it becomes active.');
            }

            // Check for active membership
            $activeMembership = Membership::where('user_id', $member->id)
                ->where('status', 'Active')
                ->orderByDesc('end_date')
                ->first();

            if ($activeMembership) {
                $startDate = $activeMembership->end_date;
                $status    = 'Pending';
            } else {
                $startDate = now();
                $status    = 'Active';
            }

            $endDate = Carbon::parse($startDate)->addDays($membershipType->duration);
            $method  = 'Cash';

            DB::beginTransaction();

            Membership::create([
                'user_id'    => $member->id,
                'type_id'    => $membershipType->type_id,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'status'     => $status,
            ]);

            Payment::create([
                'user_id'        => $member->id,
                'type_id'        => $membershipType->type_id,
                'payment_method' => $method,
                'amount'         => $membershipType->price,
                'payment_date'   => now(),
            ]);

            Notification::create([
                'user_id' => $member->id,
                'title'   => 'Membership Purchased',
                'message' => 'Your membership has been purchased successfully.',
                'type'    => 'Membership',
                'is_read' => false,
            ]);

            Notification::create([
                'user_id' => $member->id,
                'title'   => 'Payment Successful',
                'message' => 'Your payment has been processed successfully.',
                'type'    => 'Payment',
                'is_read' => false,
            ]);

            Mail::to($member->email)->send(new MembershipConfirmationMail($member, $membershipType));

            DB::commit();
            session()->forget('member_id');
            session()->forget('member_name');
            return redirect()->route('admin.membership')->with('success', 'Membership created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->forget('member_id');
            session()->forget('member_name');
            return back()->with('error', 'Failed to create membership: ' . $e->getMessage());
        }
    }

    // cancel membership
    public function cancelMembership(Request $request)
    {
        $selectedIds = $request->input('selector');

        if (! $selectedIds || ! is_array($selectedIds) || count($selectedIds) === 0) {
            return redirect()->back()->with('error', 'No memberships selected for cancellation.');
        }

        try {
            DB::transaction(function () use ($selectedIds) {
                $memberships = Membership::with('user', 'membershipType')
                    ->whereIn('membership_id', $selectedIds)
                    ->get();

                Membership::whereIn('membership_id', $selectedIds)
                    ->update(['status' => 'Cancelled']);

                foreach ($memberships as $membership) {
                    if ($membership->user && $membership->user->email) {
                        Mail::to($membership->user->email)->send(
                            new MembershipCancelledMail(
                                $membership->user,
                                $membership->membershipType->type_name ?? 'Membership',
                                $membership->start_date->format('Y-m-d'),
                                $membership->end_date->format('Y-m-d')
                            )
                        );
                        Notification::create([
                            'user_id' => $membership->user->id,
                            'title'   => 'Membership Cancelled',
                            'message' => 'Your membership has been cancelled.',
                            'type'    => 'Membership',
                            'is_read' => false,
                        ]);
                    }
                }
            });

            return redirect()->back()->with('success', count($selectedIds) . ' membership(s) cancelled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while cancelling memberships: ' . $e->getMessage());
        }
    }

    // get logged in membership data
    public function getLoggedInMembershipData(Request $request)
    {
        $userId    = auth()->id(); // get the logged-in user's ID
        $search    = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        $memberships = Membership::with('membershipType')
            ->where('user_id', $userId) // filter by logged-in user
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('membershipType', function ($typeQuery) use ($search) {
                        $typeQuery->where('type_name', 'like', "%{$search}%");
                    })->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate]);
            })
            ->orderByDesc('created_at')
            ->paginate(5);

        $membershipType = MembershipType::all();

        return view('memberDashboard.membership', compact('memberships', 'membershipType'));
    }

    // buy membership
    public function buyMembership(Request $request)
    {
        $request->validate([
            'membership_type'  => 'required|exists:membership_types,type_id',
            'membership_price' => 'required|numeric',
            'card_type'        => 'required|string',
            'card_name'        => 'required|string|max:255',
            'card_number'      => 'required|digits:16',
            'cvv'              => 'required|digits:3',
            'expiry_month'     => 'required|digits:2',
            'expiry_year'      => 'required|digits:4',
        ]);

        $user = auth()->user();

        $membershipType = MembershipType::find($request->membership_type);
        if (! $membershipType) {
            return back()->with('error', 'Invalid membership type selected.');
        }

        $pendingMembership = Membership::where('user_id', $user->id)
            ->where('status', 'Pending')
            ->first();

        if ($pendingMembership) {
            return back()->with('error', 'You already have a pending membership. Please wait until it becomes active.');
        }

        // Check for existing active membership
        $activeMembership = Membership::where('user_id', $user->id)
            ->where('status', 'Active')
            ->orderByDesc('end_date')
            ->first();

        if ($activeMembership) {
            $startDate = $activeMembership->end_date;
            $status    = 'Pending';
        } else {
            $startDate = now();
            $status    = 'Active';
        }

        $endDate = Carbon::parse($startDate)->addDays($membershipType->duration);

        DB::beginTransaction();

        try {
            $membership = Membership::create([
                'user_id'    => $user->id,
                'type_id'    => $membershipType->type_id,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'status'     => $status,
            ]);

            Payment::create([
                'user_id'        => $user->id,
                'type_id'        => $membershipType->type_id,
                'payment_method' => 'Card',
                'amount'         => $membershipType->price,
                'payment_date'   => now(),
            ]);

            Mail::to($user->email)->send(new MembershipConfirmationMail($user, $membershipType));

            Notification::create([
                'user_id' => $user->id,
                'title'   => 'Membership Purchased',
                'message' => 'Your membership has been purchased successfully.',
                'type'    => 'Membership',
                'is_read' => false,
            ]);

            Notification::create([
                'user_id' => $user->id,
                'title'   => 'Payment Successful',
                'message' => 'Your payment has been processed successfully.',
                'type'    => 'Payment',
                'is_read' => false,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Membership purchased successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while purchasing membership: ' . $e->getMessage());
        }
    }
}
