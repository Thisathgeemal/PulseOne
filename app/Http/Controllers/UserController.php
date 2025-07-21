<?php
namespace App\Http\Controllers;

use App\Mail\AdminAccountDeleteMail;
use App\Mail\AdminRegistrationMail;
use App\Mail\AdminUpdatedMail;
use App\Mail\TrainerAccountDeleteMail;
use App\Mail\TrainerRegistrationMail;
use App\Mail\TrainerUpdatedMail;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    // display trainer details
    public function getTrainerData(Request $request)
    {
        $search = $request->input('search');

        $trainers = User::whereHas('roles', function ($query) {
            $query->where('role_name', 'Trainer');
        })
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('first_name', 'LIKE', "%$search%")
                        ->orWhere('last_name', 'LIKE', "%$search%")
                        ->orWhere('email', 'LIKE', "%$search%")
                        ->orWhere('mobile_number', 'LIKE', "%$search%")
                        ->orWhere('address', 'LIKE', "%$search%");
                });
            })
            ->with(['roles' => function ($query) {
                $query->where('role_name', 'Trainer');
            }])
            ->paginate(5)
            ->appends(['search' => $search]);

        return view('adminDashboard.trainer', compact('trainers'));
    }

    // create trainer
    public function createTrainer(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|string',
            'last_name'      => 'required|string',
            'email'          => 'required|email',
            'contact_number' => [
                'required',
                'regex:/^07[0-9]{8}$/',
            ],
        ]);

        $defaultPassword = 'pulseone@2025';
        $loginUrl        = route('login');

        DB::beginTransaction();

        try {
            $user = User::where('email', $request->email)->first();

            $trainerRole = Role::where('role_name', 'Trainer')->first();
            if (! $trainerRole) {
                throw new \Exception('Trainer role not found in the system.');
            }

            if ($user) {
                $hasTrainerRole = UserRole::where('user_id', $user->id)
                    ->where('role_id', $trainerRole->role_id)
                    ->exists();

                if ($hasTrainerRole) {
                    return redirect()->route('admin.trainer')->with('error', 'This email is already assigned to a Trainer.');
                }

                UserRole::create([
                    'user_id' => $user->id,
                    'role_id' => $trainerRole->role_id,
                ]);

                if (! $user->is_active) {
                    $user->update(['is_active' => true]);
                }

                Mail::to($user->email)->send(new TrainerRegistrationMail($user, 'Existing password', $loginUrl));

                DB::commit();
                return redirect()->route('admin.trainer')->with('success', 'Trainer role assigned to existing user.');
            }

            $user = User::create([
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'email'         => $request->email,
                'mobile_number' => $request->contact_number,
                'password'      => Hash::make($defaultPassword),
            ]);

            if (! $user) {
                throw new \Exception('Failed to create trainer.');
            }

            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $trainerRole->role_id,
            ]);

            Mail::to($user->email)->send(new TrainerRegistrationMail($user, $defaultPassword, $loginUrl));

            DB::commit();
            return redirect()->route('admin.trainer')->with('success', 'Trainer created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.trainer')->with('error', 'Trainer creation failed: ' . $e->getMessage());
        }
    }

    // handle activate, deactivate, delete trainer
    public function handleTrainerAction(Request $request)
    {
        $role = Role::where('role_name', 'Trainer')->first();
        if (! $role) {
            throw new \Exception('Trainer role not found in the system.');
        }

        $userIds = $request->input('selector');
        $action  = $request->input('action');

        if (! in_array($action, ['activate', 'deactivate', 'delete'])) {
            return redirect()->back()->with('error', 'Invalid action selected.');
        }

        if (empty($userIds)) {
            return redirect()->back()->with('error', 'No users selected.');
        }

        $roleId = $role->role_id;

        $userRoles = UserRole::whereIn('user_id', $userIds)
            ->where('role_id', $roleId)
            ->get();

        foreach ($userRoles as $userRole) {
            $isActive = $userRole->is_active;

            if (($action === 'activate' && $isActive) || ($action === 'deactivate' && ! $isActive)) {
                return redirect()->back()->with('error', 'Cannot perform action on already ' . ($isActive ? 'active' : 'inactive') . ' user(s).');
            }
        }

        try {
            DB::beginTransaction();

            if ($action === 'delete') {
                $trainers = User::whereIn('id', $userIds)->get();
                foreach ($trainers as $trainer) {
                    Mail::to($trainer->email)->send(new TrainerAccountDeleteMail($trainer));
                }

                UserRole::whereIn('user_id', $userIds)
                    ->where('role_id', $roleId)
                    ->delete();

                foreach ($userIds as $userId) {
                    $remainingRoles = UserRole::where('user_id', $userId)->count();

                    if ($remainingRoles === 0) {
                        User::where('id', $userId)->delete();
                    } else {
                        $hasActiveRole = UserRole::where('user_id', $userId)
                            ->where('is_active', true)
                            ->exists();

                        User::where('id', $userId)->update([
                            'is_active' => $hasActiveRole,
                        ]);
                    }
                }

                DB::commit();

                return redirect()->route('admin.trainer')->with('success', 'Selected trainer(s) deleted successfully.');
            } else {
                $isActive = $action === 'activate';

                UserRole::whereIn('user_id', $userIds)
                    ->where('role_id', $roleId)
                    ->update(['is_active' => $isActive]);

                foreach ($userIds as $userId) {
                    $hasActiveRole = UserRole::where('user_id', $userId)
                        ->where('is_active', true)
                        ->exists();

                    User::where('id', $userId)->update([
                        'is_active' => $hasActiveRole,
                    ]);
                }

                DB::commit();

                $status = $isActive ? 'activated' : 'deactivated';
                return redirect()->route('admin.trainer')->with('success', "Selected trainer(s) {$status} successfully.");
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    // update trainer
    public function updateTrainer(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name'     => 'required|string|max:255',
                'last_name'      => 'required|string|max:255',
                'email'          => 'required|email|max:255',
                'contact_number' => [
                    'required',
                    'regex:/^07[0-9]{8}$/',
                ],
                'trainer_id'     => 'required|integer|exists:users,id',
            ]);

            $defaultPassword = 'pulseone@2025';
            $loginUrl        = route('login');

            DB::transaction(function () use ($validated, $defaultPassword, $loginUrl) {
                $id      = $validated['trainer_id'];
                $trainer = User::findOrFail($id);

                if ($validated['email'] !== $trainer->email) {
                    $existingEmail = User::where('email', $validated['email'])
                        ->where('id', '!=', $id)
                        ->first();
                    if ($existingEmail) {
                        throw ValidationException::withMessages([
                            'email' => ['This email is already taken by another user.'],
                        ]);
                    }
                }

                $trainer->first_name    = $validated['first_name'];
                $trainer->last_name     = $validated['last_name'];
                $trainer->email         = $validated['email'];
                $trainer->mobile_number = $validated['contact_number'];
                $trainer->password      = Hash::make($defaultPassword);
                $trainer->save();

                Mail::to($trainer->email)->send(new TrainerUpdatedMail($trainer, $defaultPassword, $loginUrl));
            });

            return redirect()->route('admin.trainer')->with('success', 'Trainer updated successfully!');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Something went wrong while updating the trainer.')
                ->withInput();
        }
    }

    // display admin details
    public function getAdminData(Request $request)
    {
        $search = $request->input('search');

        $admins = User::whereHas('roles', function ($query) {
            $query->where('role_name', 'Admin');
        })
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('first_name', 'LIKE', "%$search%")
                        ->orWhere('last_name', 'LIKE', "%$search%")
                        ->orWhere('email', 'LIKE', "%$search%")
                        ->orWhere('mobile_number', 'LIKE', "%$search%")
                        ->orWhere('address', 'LIKE', "%$search%");
                });
            })
            ->with(['roles' => function ($query) {
                $query->where('role_name', 'Admin');
            }])
            ->paginate(5)
            ->appends(['search' => $search]);

        return view('adminDashboard.admin', compact('admins'));
    }

    // create admin
    public function createAdmin(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|string',
            'last_name'      => 'required|string',
            'email'          => 'required|email',
            'contact_number' => [
                'required',
                'regex:/^07[0-9]{8}$/',
            ],
        ]);

        $defaultPassword = 'pulseone@2025';
        $loginUrl        = route('login');

        DB::beginTransaction();

        try {
            $user = User::where('email', $request->email)->first();

            $adminRole = Role::where('role_name', 'Admin')->first();
            if (! $adminRole) {
                throw new \Exception('Admin role not found in the system.');
            }

            if ($user) {
                $hasadminRole = UserRole::where('user_id', $user->id)
                    ->where('role_id', $adminRole->role_id)
                    ->exists();

                if ($hasadminRole) {
                    return redirect()->route('admin.admin')->with('error', 'This email is already assigned to a Admin.');
                }

                UserRole::create([
                    'user_id' => $user->id,
                    'role_id' => $adminRole->role_id,
                ]);

                if (! $user->is_active) {
                    $user->update(['is_active' => true]);
                }

                Mail::to($user->email)->send(new AdminRegistrationMail($user, 'Existing password', $loginUrl));

                DB::commit();
                return redirect()->route('admin.admin')->with('success', 'Admin role assigned to existing user.');
            }

            $user = User::create([
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'email'         => $request->email,
                'mobile_number' => $request->contact_number,
                'password'      => Hash::make($defaultPassword),
            ]);

            if (! $user) {
                throw new \Exception('Failed to create admin.');
            }

            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $adminRole->role_id,
            ]);

            Mail::to($user->email)->send(new AdminRegistrationMail($user, $defaultPassword, $loginUrl));

            DB::commit();
            return redirect()->route('admin.admin')->with('success', 'Admin created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.admin')->with('error', 'Admin creation failed: ' . $e->getMessage());
        }
    }

    // handle activate, deactivate, delete admin
    public function handleAdminAction(Request $request)
    {
        $role = Role::where('role_name', 'Admin')->first();
        if (! $role) {
            throw new \Exception('Admin role not found in the system.');
        }

        $userIds = $request->input('selector');
        $action  = $request->input('action');

        if (! in_array($action, ['activate', 'deactivate', 'delete'])) {
            return redirect()->back()->with('error', 'Invalid action selected.');
        }

        if (empty($userIds)) {
            return redirect()->back()->with('error', 'No users selected.');
        }

        $roleId = $role->role_id;

        $userRoles = UserRole::whereIn('user_id', $userIds)
            ->where('role_id', $roleId)
            ->get();

        foreach ($userRoles as $userRole) {
            $isActive = $userRole->is_active;

            if (($action === 'activate' && $isActive) || ($action === 'deactivate' && ! $isActive)) {
                return redirect()->back()->with('error', 'Cannot perform action on already ' . ($isActive ? 'active' : 'inactive') . ' user(s).');
            }
        }

        try {
            DB::beginTransaction();

            if ($action === 'delete') {
                $admins = User::whereIn('id', $userIds)->get();
                foreach ($admins as $admin) {
                    Mail::to($admin->email)->send(new AdminAccountDeleteMail($admin));
                }

                UserRole::whereIn('user_id', $userIds)
                    ->where('role_id', $roleId)
                    ->delete();

                foreach ($userIds as $userId) {
                    $remainingRoles = UserRole::where('user_id', $userId)->count();

                    if ($remainingRoles === 0) {
                        User::where('id', $userId)->delete();
                    } else {
                        $hasActiveRole = UserRole::where('user_id', $userId)
                            ->where('is_active', true)
                            ->exists();

                        User::where('id', $userId)->update([
                            'is_active' => $hasActiveRole,
                        ]);
                    }
                }

                DB::commit();

                return redirect()->route('admin.admin')->with('success', 'Selected admin(s) deleted successfully.');
            } else {
                $isActive = $action === 'activate';

                UserRole::whereIn('user_id', $userIds)
                    ->where('role_id', $roleId)
                    ->update(['is_active' => $isActive]);

                foreach ($userIds as $userId) {
                    $hasActiveRole = UserRole::where('user_id', $userId)
                        ->where('is_active', true)
                        ->exists();

                    User::where('id', $userId)->update([
                        'is_active' => $hasActiveRole,
                    ]);
                }

                DB::commit();

                $status = $isActive ? 'activated' : 'deactivated';
                return redirect()->route('admin.admin')->with('success', "Selected admin(s) {$status} successfully.");
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    // update admin
    public function updateAdmin(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name'     => 'required|string|max:255',
                'last_name'      => 'required|string|max:255',
                'email'          => 'required|email|max:255',
                'contact_number' => [
                    'required',
                    'regex:/^07[0-9]{8}$/',
                ],
                'admin_id'       => 'required|integer|exists:users,id',
            ]);

            $defaultPassword = 'pulseone@2025';
            $loginUrl        = route('login');

            DB::transaction(function () use ($validated, $defaultPassword, $loginUrl) {
                $id    = $validated['admin_id'];
                $admin = User::findOrFail($id);

                if ($validated['email'] !== $admin->email) {
                    $existingEmail = User::where('email', $validated['email'])
                        ->where('id', '!=', $id)
                        ->first();
                    if ($existingEmail) {
                        throw ValidationException::withMessages([
                            'email' => ['This email is already taken by another user.'],
                        ]);
                    }
                }

                $admin->first_name    = $validated['first_name'];
                $admin->last_name     = $validated['last_name'];
                $admin->email         = $validated['email'];
                $admin->mobile_number = $validated['contact_number'];
                $admin->password      = Hash::make($defaultPassword);
                $admin->save();

                Mail::to($admin->email)->send(new AdminUpdatedMail($admin, $defaultPassword, $loginUrl));
            });

            return redirect()->route('admin.admin')->with('success', 'Admin updated successfully!');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Something went wrong while updating the admin.')
                ->withInput();
        }
    }

}
