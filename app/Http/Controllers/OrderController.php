<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
class OrderController extends Controller
{
    public function store(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to place an order.');
        }

        // Validate the request
        $request->validate([
            'total_amount' => 'required|numeric|min:0.01',
        ]);

        // Get the cart from the session
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }

        // Use a database transaction to ensure all queries succeed or none do
        DB::beginTransaction();

        try {
            // 1. Create the main Order record
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_date' => now(),
                'total_amount' => $request->total_amount,
                'status' => 'Pending',
                'delivery_address' => Auth::user()->address ?? 'No address provided',
            ]);

            // 2. Loop through cart items and create OrderItem records
            foreach ($cart as $productId => $details) {
                // Verify the product exists and is still in stock
                $product = Product::find($productId);
                if (!$product) {
                    throw new \Exception("Product with ID {$productId} not found.");
                }

                if ($product->in_stock == 0) {
                    throw new \Exception("Product '{$product->product_name}' is no longer in stock.");
                }

                // Create the order item
                $order->items()->create([
                    'order_id' => $order->order_id, // Explicitly set the order_id
                    'product_id' => $productId,
                    'quantity' => 1,
                    'price' => $details['price'],
                ]);

                // 3. IMPORTANT: Reserve the product by setting its stock to 0
                $product = Product::find($productId);
                $product->in_stock = 0; // This reserves the item
                $product->save();
            }

            // 4. If everything was successful, commit the transaction
            DB::commit();

            // 5. Clear the cart from the session
            session()->forget('cart');

            // 6. Redirect to the success page
            return redirect()->route('order.success')->with('success', 'Your order has been placed and is awaiting seller confirmation!');

        } catch (\Exception $e) {
            // If any error occurred, roll back all database changes
            DB::rollBack();

            // Log the error for debugging
            Log::error('Order creation failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'cart' => $cart,
                'error' => $e->getMessage()
            ]);

            // Redirect back with an error message
            return redirect()->route('cart.index')->with('error', 'Something went wrong while processing your order: ' . $e->getMessage());
        }
    }

    public function accept(OrderItem $orderItem): RedirectResponse
    {
        // Authorization: Ensure the logged-in user owns the shop for this product
        if (Auth::user()->shop?->shop_id !== $orderItem->product->shop_id) {
            abort(403, 'UNAUTHORIZED ACTION');
        }

        // Use a transaction to ensure both updates succeed
        DB::transaction(function () use ($orderItem) {
            // 1. Update the parent Order's status to 'Confirmed'
            $orderItem->order->update(['status' => 'Confirmed']);

            // 2. Update the Product's stock to 0 (making it "sold")
            $orderItem->product->update(['in_stock' => 0]);
        });

        return redirect()->route('shops.dashboard')->with('success', 'Order confirmed and marked as sold!');
    }

    /**
     * Reject a pending order.
     */
    public function reject(OrderItem $orderItem): RedirectResponse
    {
        // Authorization check
        if (Auth::user()->shop?->shop_id !== $orderItem->product->shop_id) {
            abort(403, 'UNAUTHORIZED ACTION');
        }

        // CHANGE: Wrap this in a transaction and add the stock update
        DB::transaction(function () use ($orderItem) {
            // 1. Update the parent Order's status to 'Rejected'
            $orderItem->order->update(['status' => 'Rejected']);

            // 2. IMPORTANT: Put the item back in stock
            $orderItem->product->update(['in_stock' => 1]);
        });

        return redirect()->route('shops.dashboard')->with('status', 'Order has been rejected.');
    }

    public function success()
    {
        return view('ordersuccess');
    }
}