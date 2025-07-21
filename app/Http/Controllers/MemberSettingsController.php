<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MemberSettingsController extends Controller
{
    public function index()
    {
        return view('memberDashboard.settings');
    }

   public function update(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'mobile_number' => 'nullable|string',
        'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'password' => 'nullable|string|confirmed|min:6',
    ]);

    // ✅ Handle profile image upload
    if ($request->hasFile('profile_image')) {
        $file = $request->file('profile_image');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $destination = public_path('images/profile_images');

        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $file->move($destination, $filename);
        $user->profile_image = 'images/profile_images/' . $filename;
    }

    // ✅ Update other profile fields
    $user->first_name = $request->first_name;
    $user->last_name = $request->last_name;
    $user->mobile_number = $request->mobile_number;

    // ✅ Optional password update
    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();
    Auth::setUser($user); // 🔄 Refresh session user

    return back()->with('success', 'Profile updated!');
}

public function removeImage()
{
    $user = Auth::user();

    // Delete old image if exists
    if ($user->profile_image && file_exists(public_path($user->profile_image))) {
        unlink(public_path($user->profile_image));
    }

    $user->profile_image = null;
    $user->save();
    Auth::setUser($user); // Refresh session user

    return back()->with('success', 'Profile image removed.');
}

public function checkPassword(Request $request)
{
    $request->validate([
        'password' => 'required|string',
    ]);

    $user = Auth::user();
    if (Hash::check($request->password, $user->password)) {
        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false]);
}

}
