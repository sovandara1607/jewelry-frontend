<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to place an order.');
        }

        $request->validate([
            'total_amount' => 'required|numeric|min:0.01',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }

        $apiUrl = config('services.api.url');
        $token = session('api_token');

        $response = Http::withToken($token)->post("{$apiUrl}/api/orders", [
            'total_amount' => $request->total_amount,
            'cart' => $cart,
            'delivery_address' => Auth::user()->address ?? 'No address provided',
        ]);

        if ($response->failed()) {
            $message = $response->json('message') ?? 'API server is not available.';
            return redirect()->route('cart.index')->with('error', 'Something went wrong: ' . $message);
        }

        session()->forget('cart');

        return redirect()->route('order.success')->with('success', 'Your order has been placed and is awaiting seller confirmation!');
    }

    public function accept($orderItem): RedirectResponse
    {
        $apiUrl = config('services.api.url');
        $token = session('api_token');

        $response = Http::withToken($token)->post("{$apiUrl}/api/orders/accept/{$orderItem}");

        if ($response->failed()) {
            return redirect()->route('shops.dashboard')->withErrors(['api' => 'Failed to accept order.']);
        }

        return redirect()->route('shops.dashboard')->with('success', 'Order confirmed and marked as sold!');
    }

    public function reject($orderItem): RedirectResponse
    {
        $apiUrl = config('services.api.url');
        $token = session('api_token');

        $response = Http::withToken($token)->post("{$apiUrl}/api/orders/reject/{$orderItem}");

        if ($response->failed()) {
            return redirect()->route('shops.dashboard')->withErrors(['api' => 'Failed to reject order.']);
        }

        return redirect()->route('shops.dashboard')->with('status', 'Order has been rejected.');
    }

    public function success()
    {
        return view('ordersuccess');
    }
}
