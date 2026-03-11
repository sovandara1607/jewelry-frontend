<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function index()
    {
        $apiUrl = config('services.api.url');

        $params = [];
        if (Auth::check()) {
            $token = session('api_token');
            if ($token) {
                $shopResp = Http::withToken($token)->get("{$apiUrl}/api/shops/my-shop");
                if ($shopResp->ok() && $shopResp->json('shop')) {
                    $params['exclude_shop'] = $shopResp->json('shop.shop_id');
                }
            }
        }

        $response = Http::get("{$apiUrl}/api/products/newest", $params);

        if ($response->failed()) {
            return view('home', ['products' => collect([])])->withErrors(['api' => 'API server is not available.']);
        }

        $products = collect($response->json('products'))->map(function ($p) {
            return (object) array_merge($p, [
                'images' => collect($p['images'] ?? [])->map(fn($i) => (object) $i),
            ]);
        });

        return view('home', ['products' => $products]);
    }
}
