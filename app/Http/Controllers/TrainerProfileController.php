<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TrainerProfileController extends Controller
{
    // Update trainer profile
    public function update(Request $request)
    {
        $trainer = Auth::user();

        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'mobile_number' => 'nullable|string|max:15',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'password'      => 'nullable|string|confirmed|min:6',
            'address'       => 'nullable|string|max:255',
            'dob'           => 'nullable|date',
        ]);

        if ($request->hasFile('profile_image')) {
            $file        = $request->file('profile_image');
            $filename    = 'trainer_profile_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destination = public_path('images/profile_images');

            // Create folder if it doesn't exist
            if (! file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            // Remove old image if exists
            if ($trainer->profile_image && file_exists(public_path($trainer->profile_image))) {
                unlink(public_path($trainer->profile_image));
            }

            // Save new file
            $file->move($destination, $filename);
            $trainer->profile_image = 'images/profile_images/' . $filename;
        }

        $trainer->first_name    = $request->first_name;
        $trainer->last_name     = $request->last_name;
        $trainer->mobile_number = $request->mobile_number;
        $trainer->address       = $request->address;
        $trainer->dob           = $request->dob;

        if ($request->filled('password')) {
            $trainer->password = Hash::make($request->password);
        }

        $trainer->save();
        Auth::setUser($trainer);

        Notification::create([
            'user_id' => $trainer->id,
            'title'   => 'Profile Updated',
            'message' => 'Your profile has been updated successfully.',
            'type'    => 'Profile',
            'is_read' => false,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    // Delete trainer profile image.
    public function removeImage()
    {
        $trainer = Auth::user();

        if ($trainer->profile_image && file_exists(public_path($trainer->profile_image))) {
            unlink(public_path($trainer->profile_image));
        }

        $trainer->profile_image = null;
        $trainer->save();
        Auth::setUser($trainer);

        return back()->with('success', 'Profile image removed.');
    }

    // Validate password match
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
