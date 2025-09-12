<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminProfileController extends Controller
{
    // Update admin profile
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'mobile_number' => 'nullable|string|max:15',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'password'      => 'nullable|string|confirmed|min:6',
            'address'       => 'nullable|string|max:255',
            'dob'           => 'nullable|date',
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $this->updateProfileImage($user, $request->file('profile_image'));
        }

        // Update basic details
        $user->fill([
            'first_name'    => $validated['first_name'],
            'last_name'     => $validated['last_name'],
            'mobile_number' => $validated['mobile_number'] ?? null,
            'address'       => $validated['address'] ?? null,
            'dob'           => $validated['dob'] ?? null,
        ]);

        // Update password if provided
        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        Auth::setUser($user);

        $this->notifyProfileUpdate($user);

        return back()->with('success', 'Admin profile updated successfully!');
    }

    // Remove the admin's profile image.
    public function removeImage()
    {
        $user = Auth::user();

        if ($user->profile_image) {
            $this->deleteFile($user->profile_image);
            $user->profile_image = null;
            $user->save();
        }

        Auth::setUser($user);

        return back()->with('success', 'Profile image removed.');
    }

    // Check if the provided password matches the current admin password.
    public function checkPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        return response()->json([
            'success' => Hash::check($request->password, Auth::user()->password),
        ]);
    }

    // Handle profile image upload and update the user model.
    protected function updateProfileImage($user, $file)
    {
        $filename    = 'profile_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
        $destination = public_path('images/profile_images');

        if (! file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        // Delete old image
        if ($user->profile_image) {
            $this->deleteFile($user->profile_image);
        }

        $file->move($destination, $filename);
        $user->profile_image = 'images/profile_images/' . $filename;
    }

    // Delete a file safely
    protected function deleteFile($path)
    {
        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    // Send a notification for profile update
    protected function notifyProfileUpdate($user)
    {
        Notification::create([
            'user_id' => $user->id,
            'title'   => 'Profile Updated',
            'message' => 'Your profile has been updated successfully.',
            'type'    => 'Profile',
            'is_read' => false,
        ]);
    }
}
