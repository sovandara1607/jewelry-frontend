<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    public function showProfile()
    {
        $user = Auth::user();
        $apiUrl = config('services.api.url');
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('login');
        }

        $response = Http::withToken($token)->get("{$apiUrl}/api/user/orders");

        if ($response->failed()) {
            return view('profile', [
                'user' => $user,
                'orders' => collect([]),
                'pendingOrders' => collect([]),
                'confirmedOrders' => collect([]),
            ])->withErrors(['api' => 'API server is not available.']);
        }

        $data = $response->json();

        $pendingOrders = collect($data['pendingOrders'] ?? [])->map(function ($o) {
            return (object) array_merge($o, [
                'items' => collect($o['items'] ?? [])->map(function ($item) {
                    return (object) array_merge($item, [
                        'product' => (object) array_merge($item['product'] ?? [], [
                            'images' => collect($item['product']['images'] ?? [])->map(fn($i) => (object) $i),
                        ]),
                    ]);
                }),
            ]);
        });

        $confirmedOrders = collect($data['confirmedOrders'] ?? [])->map(function ($o) {
            return (object) array_merge($o, [
                'items' => collect($o['items'] ?? [])->map(function ($item) {
                    return (object) array_merge($item, [
                        'product' => (object) array_merge($item['product'] ?? [], [
                            'shop' => isset($item['product']['shop']) ? (object) $item['product']['shop'] : null,
                        ]),
                    ]);
                }),
            ]);
        });

        return view('profile', [
            'user' => $user,
            'orders' => $confirmedOrders,
            'pendingOrders' => $pendingOrders,
            'confirmedOrders' => $confirmedOrders,
        ]);
    }
}
