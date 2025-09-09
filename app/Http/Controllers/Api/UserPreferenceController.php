<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserPreferenceController extends Controller
{
    /**
     * Get user preferences
     */
    public function show(Request $request, string $key): JsonResponse
    {
        $userId = Auth::id();
        
        if ($key === 'appearance') {
            $preferences = UserPreference::getAppearance($userId);
        } else {
            $preferences = UserPreference::get($userId, $key, []);
        }

        return response()->json([
            'success' => true,
            'data' => $preferences,
        ]);
    }

    /**
     * Update user preferences
     */
    public function update(Request $request, string $key): JsonResponse
    {
        $userId = Auth::id();

        // Validate based on preference type
        if ($key === 'appearance') {
            $validator = Validator::make($request->all(), [
                'theme_mode' => 'required|in:light,dark,auto',
                'accent_color' => 'required|regex:/^#[0-9a-f]{6}$/i',
                'sidebar_position' => 'required|in:left,right',
                'sidebar_compact' => 'required|boolean',
                'density' => 'sometimes|in:compact,comfortable,spacious',
                'font_size' => 'sometimes|in:small,normal,large',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
        }

        try {
            UserPreference::set($userId, $key, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Preferences updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update preferences',
            ], 500);
        }
    }

    /**
     * Reset user preferences to defaults
     */
    public function reset(Request $request, string $key): JsonResponse
    {
        $userId = Auth::id();

        try {
            if ($key === 'appearance') {
                $defaults = UserPreference::getDefaultAppearance();
                UserPreference::set($userId, $key, $defaults);
            } else {
                // Delete the preference to fall back to defaults
                UserPreference::where('user_id', $userId)
                    ->where('key', $key)
                    ->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Preferences reset to defaults',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset preferences',
            ], 500);
        }
    }

    /**
     * Export user preferences
     */
    public function export(Request $request): JsonResponse
    {
        $userId = Auth::id();
        
        $preferences = UserPreference::where('user_id', $userId)->get();
        $exported = [];
        
        foreach ($preferences as $pref) {
            $exported[$pref->key] = $pref->value;
        }

        return response()->json([
            'success' => true,
            'data' => $exported,
        ]);
    }

    /**
     * Import user preferences
     */
    public function import(Request $request): JsonResponse
    {
        $userId = Auth::id();
        
        $validator = Validator::make($request->all(), [
            'preferences' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            foreach ($request->preferences as $key => $value) {
                // Validate each preference type before importing
                if ($key === 'appearance') {
                    $validator = Validator::make($value, [
                        'theme_mode' => 'required|in:light,dark,auto',
                        'accent_color' => 'required|regex:/^#[0-9a-f]{6}$/i',
                        'sidebar_position' => 'required|in:left,right',
                        'sidebar_compact' => 'required|boolean',
                    ]);

                    if ($validator->fails()) {
                        continue; // Skip invalid preferences
                    }
                }

                UserPreference::set($userId, $key, $value);
            }

            return response()->json([
                'success' => true,
                'message' => 'Preferences imported successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to import preferences',
            ], 500);
        }
    }
}
