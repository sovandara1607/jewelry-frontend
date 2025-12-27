<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        // Get the cart data from the session
        $cart = session()->get('cart', []);

        // --- LOGIC TO GROUP ITEMS BY SHOP (CORRECTED) ---
        $cartItemsByShop = [];
        $totalAmount = 0;

        // The '$id' here is the 'product_id'
        foreach ($cart as $id => $details) {
            $shopName = $details['shop_name'];

            if (!isset($cartItemsByShop[$shopName])) {
                $cartItemsByShop[$shopName] = [
                    'shop_name' => $details['shop_name'],
                    'shop_email' => $details['shop_email'],
                    'items' => [] // Initialize the items array
                ];
            }

            // THE FIX: Assign the item details to a key that is the product ID.
            // This preserves the ID for the view to use.
            $cartItemsByShop[$shopName]['items'][$id] = $details;

            // Add the item's price to the total
            $totalAmount += $details['price'];
        }

        return view('cart', [
            'cartItemsByShop' => $cartItemsByShop,
            'totalAmount' => $totalAmount
        ]);
    }

    public function add(Request $request, Product $product)
    {
        // Get the current cart from the session, or create an empty array
        $cart = $request->session()->get('cart', []);

        // Check if the product is already in the cart to prevent duplicates
        if (!isset($cart[$product->product_id])) {
            // Add the new product to the cart array
            $cart[$product->product_id] = [
                //  "product_id" => $product->product_id, 
                "name" => $product->product_name,
                "price" => $product->product_price,
                "image" => $product->images->first()?->image_path,
                "shop_name" => $product->shop->shop_name ?? 'Store Name Unavailable',
                "shop_email" => $product->shop->shop_email ?? 'Contact Not Available',
            ];
        }

        // Store the updated cart back into the session
        $request->session()->put('cart', $cart);

        // Redirect back to the previous page with a success message
        return redirect()->back()->with('success', 'Item added to cart!');
    }
    public function remove(Request $request, $productId)
    {
        // Get the current cart from the session
        $cart = $request->session()->get('cart', []);

        // Check if the product exists in the cart and remove it
        if (isset($cart[$productId])) {
            unset($cart[$productId]);

            // Store the updated cart back into the session
            $request->session()->put('cart', $cart);

            return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
        }

        return redirect()->route('cart.index')->with('error', 'Item not found in cart.');
    }
}