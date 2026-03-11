<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'phonenumber' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $apiUrl = config('services.api.url');
        $response = Http::post("{$apiUrl}/api/frontend/register", [
            'name' => $request->name,
            'email' => $request->email,
            'phonenumber' => $request->phonenumber,
            'address' => $request->address,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
        ]);

        if ($response->failed()) {
            $errors = $response->json('errors') ?? [];
            $message = $response->json('message') ?? 'API server is not available.';
            if ($errors) {
                return back()->withErrors($errors)->withInput();
            }
            return back()->withErrors(['email' => $message])->withInput();
        }

        $data = $response->json();

        // Sync local user for session auth
        $user = User::updateOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'phonenumber' => $request->phonenumber,
                'address' => $request->address,
            ]
        );

        Auth::login($user);
        session(['api_token' => $data['token']]);

        return redirect(route('profile', absolute: false));
    }
}
