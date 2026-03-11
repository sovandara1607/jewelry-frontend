<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'profilepic' => 'required|image|max:2048',
        ]);

        $user = $request->user();
        $apiUrl = config('services.api.url');
        $token = session('api_token');

        // Delete old avatar locally if exists
        if ($user->profilepic) {
            Storage::disk('public')->delete($user->profilepic);
        }

        // Store new avatar locally
        $path = $request->file('profilepic')->store('avatars', 'public');

        // Update locally for immediate UI response
        $user->profilepic = $path;
        $user->save();

        // Update in API
        Http::withToken($token)->post("{$apiUrl}/api/user/avatar", [
            'profilepic_path' => $path,
        ]);

        return Redirect::route('profile')->with('status', 'avatar-updated');
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255',
            'phonenumber' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $apiUrl = config('services.api.url');
        $token = session('api_token');

        $response = Http::withToken($token)->patch("{$apiUrl}/api/user/profile", [
            'name' => $request->name,
            'email' => $request->email,
            'phonenumber' => $request->phonenumber,
            'address' => $request->address,
        ]);

        if ($response->failed()) {
            $errors = $response->json('errors') ?? [];
            if ($errors) {
                return back()->withErrors($errors)->withInput();
            }
            return back()->withErrors(['api' => 'Failed to update profile.'])->withInput();
        }

        // Sync local user
        $user = $request->user();
        $user->fill([
            'name' => $request->name,
            'email' => $request->email,
            'phonenumber' => $request->phonenumber,
            'address' => $request->address,
        ]);
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        return Redirect::route('profile')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $apiUrl = config('services.api.url');
        $token = session('api_token');

        // Delete profile via API
        Http::withToken($token)->delete("{$apiUrl}/api/user/profile");

        $user = $request->user();
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
