<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $apiUrl = config('services.api.url');

        $params = [];
        if ($request->filled('search')) {
            $params['search'] = $request->search;
        }
        if ($request->filled('category') && $request->category !== 'all') {
            $params['category'] = $request->category;
        }
        if ($request->filled('sort')) {
            $params['sort'] = $request->sort;
        }
        if (Auth::check()) {
            $token = session('api_token');
            if ($token) {
                $shopResp = Http::withToken($token)->get("{$apiUrl}/api/shops/my-shop");
                if ($shopResp->ok() && $shopResp->json('shop')) {
                    $params['exclude_shop'] = $shopResp->json('shop.shop_id');
                }
            }
        }

        $response = Http::get("{$apiUrl}/api/products/browse", $params);

        if ($response->failed()) {
            return view('shopall', ['products' => collect([])])->withErrors(['api' => 'API server is not available.']);
        }

        $products = collect($response->json('products'))->map(function ($p) {
            $shop = isset($p['shop']) ? (object) $p['shop'] : null;
            return (object) array_merge($p, [
                'images' => collect($p['images'] ?? [])->map(fn($i) => (object) $i),
                'shop' => $shop,
            ]);
        });

        return view('shopall', ['products' => $products]);
    }

    public function create()
    {
        return view('shops.create');
    }

    public function dashboard()
    {
        $apiUrl = config('services.api.url');
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('login');
        }

        $response = Http::withToken($token)->get("{$apiUrl}/api/shops/my-dashboard");

        if ($response->failed()) {
            return redirect()->route('shops.create')->withErrors(['api' => 'API server is not available.']);
        }

        $data = $response->json();

        if (!$data['shop']) {
            return redirect()->route('shops.create')->with('info', 'You need to create a shop first!');
        }

        $shop = (object) $data['shop'];
        $listings = collect($data['listings'] ?? [])->map(function ($l) {
            return (object) array_merge($l, [
                'images' => collect($l['images'] ?? [])->map(fn($i) => (object) $i),
            ]);
        });
        $pendingOrders = collect($data['pendingOrders'] ?? [])->map(function ($o) {
            return (object) array_merge($o, [
                'product' => (object) array_merge($o['product'] ?? [], [
                    'images' => collect($o['product']['images'] ?? [])->map(fn($i) => (object) $i),
                ]),
                'order' => (object) array_merge($o['order'] ?? [], [
                    'user' => isset($o['order']['user']) ? (object) $o['order']['user'] : null,
                ]),
            ]);
        });
        $confirmedOrders = collect($data['confirmedOrders'] ?? [])->map(function ($o) {
            return (object) array_merge($o, [
                'product' => (object) array_merge($o['product'] ?? [], [
                    'images' => collect($o['product']['images'] ?? [])->map(fn($i) => (object) $i),
                ]),
                'order' => (object) array_merge($o['order'] ?? [], [
                    'user' => isset($o['order']['user']) ? (object) $o['order']['user'] : null,
                ]),
            ]);
        });

        return view('shops.dashboard', [
            'shop' => $shop,
            'listings' => $listings,
            'pendingOrders' => $pendingOrders,
            'confirmedOrders' => $confirmedOrders,
        ]);
    }

    public function showPublic($handle)
    {
        $apiUrl = config('services.api.url');
        $response = Http::get("{$apiUrl}/api/shops/{$handle}/public");

        if ($response->failed()) {
            abort(404);
        }

        $data = $response->json();
        $shop = (object) $data['shop'];
        $products = collect($data['products'] ?? [])->map(function ($p) {
            return (object) array_merge($p, [
                'images' => collect($p['images'] ?? [])->map(fn($i) => (object) $i),
            ]);
        });

        return view('shops.sellerpage', [
            'seller' => $shop,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'shop_name' => 'required|string|max:100',
            'shop_email' => 'required|email|max:100',
            'shop_phonenumber' => 'required|string|max:20',
            'shop_address' => 'required|string|max:255',
            'shop_description' => 'nullable|string',
        ]);

        $apiUrl = config('services.api.url');
        $token = session('api_token');

        $response = Http::withToken($token)->post("{$apiUrl}/api/shops", [
            'shop_name' => $request->shop_name,
            'shop_email' => $request->shop_email,
            'shop_phonenumber' => $request->shop_phonenumber,
            'shop_address' => $request->shop_address,
            'shop_description' => $request->shop_description,
        ]);

        if ($response->failed()) {
            $errors = $response->json('errors') ?? [];
            $message = $response->json('message') ?? 'API server is not available.';
            if ($errors) {
                return back()->withErrors($errors)->withInput();
            }
            return back()->withErrors(['api' => $message])->withInput();
        }

        return redirect()->route('shops.dashboard')->with('status', 'Shop created successfully!');
    }

    public function edit()
    {
        $apiUrl = config('services.api.url');
        $token = session('api_token');

        $response = Http::withToken($token)->get("{$apiUrl}/api/shops/my-shop");

        if ($response->failed() || !$response->json('shop')) {
            return redirect()->route('shops.create');
        }

        $shop = (object) $response->json('shop');
        return view('shops.edit', ['shop' => $shop]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'shop_name' => 'required|string|max:100',
            'shop_email' => 'required|email|max:100',
            'shop_phonenumber' => 'required|string|max:20',
            'shop_address' => 'required|string|max:255',
            'shop_description' => 'nullable|string',
        ]);

        $apiUrl = config('services.api.url');
        $token = session('api_token');

        // First get the shop ID
        $shopResp = Http::withToken($token)->get("{$apiUrl}/api/shops/my-shop");
        if ($shopResp->failed() || !$shopResp->json('shop')) {
            return back()->withErrors(['api' => 'Could not find your shop.']);
        }
        $shopId = $shopResp->json('shop.shop_id');

        $response = Http::withToken($token)->patch("{$apiUrl}/api/shops/{$shopId}", [
            'shop_name' => $request->shop_name,
            'shop_email' => $request->shop_email,
            'shop_phonenumber' => $request->shop_phonenumber,
            'shop_address' => $request->shop_address,
            'shop_description' => $request->shop_description,
        ]);

        if ($response->failed()) {
            $errors = $response->json('errors') ?? [];
            if ($errors) {
                return back()->withErrors($errors)->withInput();
            }
            return back()->withErrors(['api' => 'Failed to update shop.'])->withInput();
        }

        return redirect()->route('shops.dashboard')->with('status', 'Shop details updated successfully!');
    }

    public function updatePicture(Request $request): RedirectResponse
    {
        $request->validate([
            'shop_profilepic' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $apiUrl = config('services.api.url');
        $token = session('api_token');

        // Get shop ID
        $shopResp = Http::withToken($token)->get("{$apiUrl}/api/shops/my-shop");
        if ($shopResp->failed() || !$shopResp->json('shop')) {
            return back()->withErrors(['api' => 'Could not find your shop.']);
        }
        $shopId = $shopResp->json('shop.shop_id');

        // Upload the actual image file to API server
        $response = Http::withToken($token)
            ->attach('shop_profilepic', file_get_contents($request->file('shop_profilepic')->getRealPath()), $request->file('shop_profilepic')->getClientOriginalName())
            ->post("{$apiUrl}/api/shops/{$shopId}/picture");

        if ($response->failed()) {
            return back()->withErrors(['api' => 'Failed to update shop picture.']);
        }

        return redirect()->route('shops.dashboard')->with('status', 'Shop picture updated successfully!');
    }
}