@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
@endsection

@section('content')
    <div class="cart-container">
        <h1 class="cart-title">Shopping Cart</h1>

        @if (empty($cartItemsByShop))
            <div class="empty-state">
                <p>Your shopping cart is empty.</p>
                <a href="{{ route('shop.index') }}" class="btn-dark">Continue Shopping</a>
            </div>
        @else
            @foreach ($cartItemsByShop as $shopName => $shopGroup)

                <div class="cart-shop-group">
                    <div class="cart-shop-header">
                        <div>
                            <div class="shop-name">{{ $shopGroup['shop_name'] }}</div>
                            <div class="shop-handle">{{ $shopGroup['shop_email'] }}</div>

                        </div>
                                <a href="{{ route('shops.public', ['handle' => $shopGroup['shop_name']]) }}" class="arrow-icon">

                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </a>
                    </div>

                    @foreach ($shopGroup['items'] as $productId =>$item)
                        <div class="cart-item" style="margin-bottom: 16px;">
                            <div class="cart-item-image">
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}">
                            </div>
                            <div class="cart-item-details">
                                <div class="item-name">{{ $item['name'] }}</div>
                                <div class="item-price">${{ number_format($item['price'], 2) }}</div>
                            </div>
                            <div class="cart-item-actions">
                                <div class="item-subtotal">${{ number_format($item['price'], 2) }}</div>
                               <form action="{{ route('cart.remove', ['product' => $productId]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="remove-btn" aria-label="Remove item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

            <div class="cart-summary">
                {{-- This new section displays the calculated total --}}
                <div class="cart-total">
                    <span class="total-label">Subtotal:</span>
                    <span class="total-price">${{ number_format($totalAmount, 2) }}</span>
                </div>

                {{-- Change the checkout link to a form --}}
            <form action="{{ route('order.store') }}" method="POST">
                @csrf
                <input type="hidden" name="total_amount" value="{{ $totalAmount }}">
                <button type="submit" class="checkout-btn">Check Out</button>
            </form>
            </div>
        @endif
    </div>
@endsection