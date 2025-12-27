<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
   public function index()
    {
        // Start with a query builder instance
        $query = Product::query();

        // Check if a user is logged in and has a shop
        if (Auth::check() && Auth::user()->shop) {
            // If so, exclude products where the shop_id matches their own shop_id
            $query->where('shop_id', '!=', Auth::user()->shop->shop_id);
        }

        // Get the 4 most recently created products that are in stock
        // and eager load their images for efficiency
        $newestProducts = $query->where('in_stock', '>', 0)
                                ->with('images')
                                ->latest('date_created')
                                ->take(4)
                                ->get();

        return view('home', ['products' => $newestProducts]);
    }
}