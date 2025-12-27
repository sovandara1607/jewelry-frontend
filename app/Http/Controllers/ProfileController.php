<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage; 

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'profilepic' => 'required|image|max:2048', // Must be an image, max 2MB
        ]);

        $user = $request->user();

        // Delete old avatar if it exists
        if ($user->profilepic) {
            Storage::disk('public')->delete($user->profilepic);
        }

        // Store the new avatar
        $path = $request->file('profilepic')->store('avatars', 'public');

        // Update the user's profilepic column
        $user->profilepic = $path;
        $user->save();

        return Redirect::route('profile')->with('status', 'avatar-updated');
    }

      public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // This gets the validated data from the form request
        $request->user()->fill($request->validated());

        // If the user changed their email, we need to reset the verification status
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Save the changes to the database
        $request->user()->save();

        return Redirect::route('profile')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
