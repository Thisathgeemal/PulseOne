<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{

    // Update admin profile
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'mobile_number' => 'nullable|string|max:15',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'password'      => 'nullable|string|confirmed|min:6',
        ]);

        // Upload new profile image
        if ($request->hasFile('profile_image')) {
            $file        = $request->file('profile_image');
            $filename    = 'profile_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('images/profile_images');

            if (! file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            // Delete old image
            if ($user->profile_image && file_exists(public_path($user->profile_image))) {
                unlink(public_path($user->profile_image));
            }

            $file->move($destination, $filename);
            $user->profile_image = 'images/profile_images/' . $filename;
        }

        // Update name and mobile
        $user->first_name    = $request->first_name;
        $user->last_name     = $request->last_name;
        $user->mobile_number = $request->mobile_number;

        // Update password if filled
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        Auth::setUser($user);

        return back()->with('success', 'Admin profile updated successfully!');
    }

    // Remove the admin's profile image.

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
}
