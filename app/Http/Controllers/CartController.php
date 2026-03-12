<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        $cartItemsByShop = [];
        $totalAmount = 0;

        foreach ($cart as $id => $details) {
            // Ensure shop_name is a string (handle legacy data)
            $shopName = is_object($details['shop_name'] ?? null)
                ? (isset($details['shop_name']->shop_name) ? $details['shop_name']->shop_name : 'Unknown Shop')
                : ($details['shop_name'] ?? 'Unknown Shop');

            $shopEmail = is_object($details['shop_email'] ?? null)
                ? (isset($details['shop_email']->shop_email) ? $details['shop_email']->shop_email : 'N/A')
                : ($details['shop_email'] ?? 'N/A');

            if (!isset($cartItemsByShop[$shopName])) {
                $cartItemsByShop[$shopName] = [
                    'shop_name' => (string) $shopName,
                    'shop_email' => (string) $shopEmail,
                    'items' => [],
                ];
            }

            $cartItemsByShop[$shopName]['items'][$id] = $details;
            $totalAmount += $details['price'];
        }

        return view('cart', [
            'cartItemsByShop' => $cartItemsByShop,
            'totalAmount' => $totalAmount,
        ]);
    }

    public function add(Request $request, $product)
    {
        $apiUrl = config('services.api.url');
        $response = Http::get("{$apiUrl}/api/products/{$product}/detail");

        if ($response->failed()) {
            return redirect()->back()->with('error', 'Could not add item. API is unavailable.');
        }

        $productData = $response->json('product');
        $cart = $request->session()->get('cart', []);

        $productId = $productData['product_id'];

        if (!isset($cart[$productId])) {
            $firstImage = $productData['images'][0]['image_path'] ?? null;

            // Ensure shop data is properly extracted as strings
            $shopName = 'Store Name Unavailable';
            $shopEmail = 'Contact Not Available';

            if (isset($productData['shop'])) {
                $shop = $productData['shop'];
                if (is_array($shop)) {
                    $shopName = $shop['shop_name'] ?? $shopName;
                    $shopEmail = $shop['shop_email'] ?? $shopEmail;
                } elseif (is_object($shop)) {
                    $shopName = $shop->shop_name ?? $shopName;
                    $shopEmail = $shop->shop_email ?? $shopEmail;
                }
            }

            $cart[$productId] = [
                'name' => (string) $productData['product_name'],
                'price' => $productData['product_price'],
                'image' => $firstImage,
                'shop_name' => (string) $shopName,
                'shop_email' => (string) $shopEmail,
            ];
        }

        $request->session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Item added to cart!');
    }

    public function remove(Request $request, $productId)
    {
        $cart = $request->session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $request->session()->put('cart', $cart);

            return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
        }

        return redirect()->route('cart.index')->with('error', 'Item not found in cart.');
    }

    public function clear(Request $request)
    {
        $request->session()->forget('cart');
        return redirect()->route('shop.index')->with('success', 'Cart has been cleared.');
    }
}
