<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
     public function showProfile()
    {
        $user = Auth::user();
        // 1. Get PENDING orders placed by this user
    $pendingOrders = $user->orders()
    ->where('status', 'Pending')
    ->with('items.product.images')->get();

    // 2. Get CONFIRMED orders placed by this user
    $confirmedOrders = $user->orders()
    ->where('status', 'Confirmed')
    ->with('items.product.shop')->get();


        // Get the user's orders. This will be an empty collection for now.
        // We assume an 'orders' relationship exists on the User model.
        $orders = $user->orders()->latest()->get(); 

        return view('profile', [
            'user' => $user,
            'orders' => $confirmedOrders,
            'pendingOrders' => $pendingOrders,
        'confirmedOrders' => $confirmedOrders
        ]);
    }
}