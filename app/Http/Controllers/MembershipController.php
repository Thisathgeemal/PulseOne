<?php
namespace App\Http\Controllers;

use App\Mail\MembershipCancelledMail;
use App\Mail\MembershipConfirmationMail;
use App\Models\Membership;
use App\Models\MembershipType;
use App\Models\Payment;
use App\Models\User;
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

            // Check for active membership
            $hasActiveMembership = Membership::where('user_id', $member->id)
                ->where('status', 'Active')
                ->exists();

            if ($hasActiveMembership) {
                return back()->with('error', 'The user already has an active membership.');
            }

            $membershipType = MembershipType::find($request->membership_type);
            if (! $membershipType) {
                return back()->with('error', 'Invalid membership type selected.');
            }

            DB::beginTransaction();

            $startDate = now();
            $endDate   = $startDate->copy()->addDays($membershipType->duration);
            $method    = 'Cash';

            Membership::create([
                'user_id'    => $member->id,
                'type_id'    => $membershipType->type_id,
                'start_date' => $startDate,
                'end_date'   => $endDate,
            ]);

            Payment::create([
                'user_id'        => $member->id,
                'type_id'        => $membershipType->type_id,
                'payment_method' => $method,
                'amount'         => $membershipType->price,
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
                    }
                }
            });

            return redirect()->back()->with('success', count($selectedIds) . ' membership(s) cancelled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while cancelling memberships: ' . $e->getMessage());
        }
    }

}
