<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Update basic profile info (name, phone, address, profile picture)
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'            => 'required|string|max:255',
            'phone_number'    => 'nullable|string|max:50',
            'address'         => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        $user->name         = $request->name;
        $user->phone_number = $request->phone_number;
        $user->address      = $request->address;

      if ($request->hasFile('profile_picture')) {
    if ($user->profile_picture && !str_starts_with($user->profile_picture, 'data:')) {
        Storage::disk('public')->delete($user->profile_picture);
    }
    $path = $request->file('profile_picture')->store('profile-pictures', 'public');
    $user->profile_picture = $path;
}

$user->save();

return response()->json(['success' => true, 'user' => [
    'name'            => $user->name,
    'phone_number'    => $user->phone_number,
    'address'         => $user->address,
    'profile_picture' => $user->profile_picture ? Storage::url($user->profile_picture) : null,
]]);
    }

    /**
     * Update display preferences (dark mode, font size)
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'dark_mode' => 'required|in:0,1',
            'font_size' => 'required|in:small,medium,large',
        ]);

        $user->dark_mode = $request->dark_mode;
        $user->font_size = $request->font_size;
        $user->save();

        return response()->json(['success' => true]);
    }

    /**
     * Verify if current password is correct (AJAX, no actual change)
     */
    public function verifyPassword(Request $request)
    {
        $user = Auth::user();
        $request->validate(['current_password' => 'required|string']);

        return response()->json([
            'valid' => Hash::check($request->current_password, $user->password)
        ]);
    }

    /**
     * Change password — verifies current password first
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password'          => 'required|string',
            'new_password'              => 'required|string|min:8|confirmed',
        ]);

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        // Make sure new password is different
        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'New password must be different from current password.',
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
        ]);
    }
}