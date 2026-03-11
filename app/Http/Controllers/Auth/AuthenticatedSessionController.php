<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $apiUrl = config('services.api.url');
        $response = Http::post("{$apiUrl}/api/frontend/login", [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($response->failed()) {
            throw ValidationException::withMessages([
                'email' => $response->json('message') ?? 'API server is not available.',
            ]);
        }

        $data = $response->json();

        // Sync local user for session auth
        $user = User::updateOrCreate(
            ['email' => $data['user']['email']],
            [
                'name' => $data['user']['name'],
                'password' => Hash::make($request->password),
                'phonenumber' => $data['user']['phonenumber'] ?? null,
                'address' => $data['user']['address'] ?? null,
            ]
        );

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        session(['api_token' => $data['token']]);

        return redirect()->route('profile');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $apiUrl = config('services.api.url');
        $token = session('api_token');
        if ($token) {
            Http::withToken($token)->post("{$apiUrl}/api/frontend/logout");
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/home');
    }
}
