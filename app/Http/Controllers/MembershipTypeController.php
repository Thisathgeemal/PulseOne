<?php
namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\MembershipType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MembershipTypeController extends Controller
{
    // display membershiptype details
    public function getMembertypeData(Request $request)
    {
        $search = $request->input('search');

        $membershipType = MembershipType::when($search, function ($query, $search) {
            $query->where('type_name', 'like', "%{$search}%")
                ->orWhere('duration', 'like', "%{$search}%")
                ->orWhere('price', 'like', "%{$search}%");
        })
            ->orderByDesc('created_at')
            ->paginate(5);

        return view('adminDashboard.membertype', compact('membershipType'));
    }

    // create membertype
    public function createMembertype(Request $request)
    {
        $validated = $request->validate([
            'type_name' => 'required|string|max:255|unique:membership_types,type_name',
            'duration'  => 'required|integer|min:1',
            'price'     => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();
            MembershipType::create([
                'type_name' => $validated['type_name'],
                'duration'  => $validated['duration'],
                'price'     => $validated['price'],
            ]);
            DB::commit();

            return redirect()->back()->with('success', 'Membership type created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Failed to create membership type.' . $e->getMessage());
        }
    }

    // update membertype
    public function updateMembertype(Request $request)
    {
        $validated = $request->validate([
            'type_id'   => 'required|exists:membership_types,type_id',
            'type_name' => 'required|string|max:255',
            'duration'  => 'required|integer|min:1',
            'price'     => 'required|numeric|min:0',
        ]);

        $conflict = MembershipType::where('type_name', $request->type_name)
            ->where('type_id', '!=', $request->type_id)
            ->exists();

        if ($conflict) {
            return redirect()->back()->with('error', "Type name '{$request->type_name}' already exists for another type_id!");
        }

        try {
            DB::beginTransaction();

            $membershipType = MembershipType::findOrFail($validated['type_id']);

            $membershipType->update([
                'type_name' => $validated['type_name'],
                'duration'  => $validated['duration'],
                'price'     => $validated['price'],
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Membership type updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Failed to update membership type.' . $e->getMessage());
        }
    }

    // delete membertype
    public function deleteMembertype(Request $request)
    {
        $selectedIds = $request->input('selector');

        if (! $selectedIds || ! is_array($selectedIds) || count($selectedIds) === 0) {
            return redirect()->back()->with('error', 'No membership types selected for deletion.');
        }

        try {
            DB::transaction(function () use ($selectedIds) {
                foreach ($selectedIds as $typeId) {
                    $typeExists = MembershipType::where('type_id', $typeId)->exists();
                    if (! $typeExists) {
                        throw new \Exception("Membership type ID {$typeId} does not exist.");
                    }

                    $hasActiveMembership = Membership::where('type_id', $typeId)
                        ->where('status', 'Active')
                        ->exists();

                    if ($hasActiveMembership) {
                        throw new \Exception("Cannot delete type ID {$typeId} because it is linked to active memberships.");
                    }
                }

                MembershipType::whereIn('type_id', $selectedIds)->delete();
            });

            return redirect()->back()->with('success', count($selectedIds) . ' membership type(s) deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while deleting membership types: ' . $e->getMessage());
        }
    }
}
