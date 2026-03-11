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
            $shopName = $details['shop_name'];

            if (!isset($cartItemsByShop[$shopName])) {
                $cartItemsByShop[$shopName] = [
                    'shop_name' => $details['shop_name'],
                    'shop_email' => $details['shop_email'],
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
            $cart[$productId] = [
                'name' => $productData['product_name'],
                'price' => $productData['product_price'],
                'image' => $firstImage,
                'shop_name' => $productData['shop']['shop_name'] ?? 'Store Name Unavailable',
                'shop_email' => $productData['shop']['shop_email'] ?? 'Contact Not Available',
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
}
