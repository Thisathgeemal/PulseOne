<?php
namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{

    // Display role details
    public function getRoleData(Request $request)
    {
        $search = $request->input('search');

        $roles = Role::withCount('users')
            ->when($search, fn($query) => $query->where('role_name', 'LIKE', "%$search%"))
            ->paginate(5)
            ->appends(['search' => $search]);

        return view('adminDashboard.role', compact('roles'));
    }

    // Create roles
    public function createRole(Request $request)
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:255|unique:roles,role_name',
        ]);

        try {
            Role::create(['role_name' => $validated['role_name']]);
            return redirect()->route('admin.role')->with('success', 'Role created successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.role')->with('error', 'Failed to create role: ' . $e->getMessage());
        }
    }

    // Delete roles
    public function deleteRole(Request $request)
    {
        $selectedRoles = $request->input('selector');

        if (empty($selectedRoles) || ! is_array($selectedRoles)) {
            return redirect()->back()->with('error', 'No role selected for deletion.');
        }

        try {
            DB::beginTransaction();

            foreach ($selectedRoles as $roleId) {
                $role = Role::find($roleId);

                if (! $role) {
                    return redirect()->back()->with('error', 'Role(s) not found.');
                }

                // Deactivate user roles
                UserRole::where('role_id', $roleId)->update(['is_active' => false]);

                // Update user active status based on remaining active roles
                $affectedUserIds = UserRole::where('role_id', $roleId)->pluck('user_id');
                foreach ($affectedUserIds as $userId) {
                    $hasActiveRoles = UserRole::where('user_id', $userId)
                        ->where('is_active', true)
                        ->exists();

                    User::where('id', $userId)->update(['is_active' => $hasActiveRoles]);
                }

                $role->delete();
            }

            DB::commit();
            return redirect()->route('admin.role')->with('success', 'Selected role(s) deleted successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while deleting roles: ' . $e->getMessage());
        }
    }
}
