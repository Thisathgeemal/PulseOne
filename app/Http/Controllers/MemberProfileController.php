<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MemberProfileController extends Controller
{

    // Update member profile
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'mobile_number' => 'nullable|string|max:15',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'password'      => 'nullable|string|confirmed|min:6',
            'address'       => 'nullable|string|max:255',
            'dob'           => 'nullable|date',
        ]);

        // Profile Image Upload
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');

            // Generate a unique filename
            $filename    = 'profile_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('images/profile_images');

            // Create directory if it doesn't exist
            if (! file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            // Delete old image if it exists
            if ($user->profile_image && file_exists(public_path($user->profile_image))) {
                unlink(public_path($user->profile_image));
            }

            // Save new image
            $moved     = $file->move($destination, $filename);
            $savedPath = 'images/profile_images/' . $filename;

            // Check if file moved successfully
            if (file_exists(public_path($savedPath))) {
                $user->profile_image = $savedPath;
            } else {
                Log::error('Profile image failed to save.', ['file' => $filename]);
            }
        }

        // Basic Info
        $user->first_name    = $request->first_name;
        $user->last_name     = $request->last_name;
        $user->mobile_number = $request->mobile_number;
        $user->address       = $request->address;
        $user->dob           = $request->dob;

        // Optional Password
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Refresh session user
        Auth::setUser($user);

        return back()->with('success', 'Profile updated successfully!');
    }

    // Remove profile image
    public function removeImage()
    {
        $user = Auth::user();

        if ($user->profile_image && file_exists(public_path($user->profile_image))) {
            unlink(public_path($user->profile_image));
        }

        $user->profile_image = null;
        $user->save();

        Auth::setUser($user);

        return back()->with('success', 'Profile image removed.');
    }

    // Check if entered password matches current password.
    public function checkPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        return response()->json([
            'success' => Hash::check($request->password, Auth::user()->password),
        ]);
    }
}
