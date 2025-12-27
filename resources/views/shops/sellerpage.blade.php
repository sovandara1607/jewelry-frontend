@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/seller-page.css') }}">
@endsection


@section('content')
<div class="profile-container">
     <a href="#" class="page-back-link" id="page-back-link">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        <span>Back</span>
    </a>
    {{-- Display the real seller info --}}
    <div class="profile-header">
        <div class="profile-avatar">
            @if ($seller->shop_profilepic)
                <img src="{{ asset('storage/' . $seller->shop_profilepic) }}" alt="{{ $seller->shop_name }}'s logo">
            @endif
        </div>
        <div class="profile-user-details">
            <h2>{{ $seller->shop_name }}</h2>
            {{-- We can display the shop email or user's name --}}
            <p class="username">{{ $seller->shop_email }}</p>
            <p class="location">Located in {{ $seller->shop_address }}</p>
        </div>
    </div>

    {{-- Display the real shop description --}}
    <div class="shop-description-section">
    <p>{{ $seller->shop_description }}</p>
</div>

    <div class="seller-products-header">
        <h3>All Jewelries from {{ $seller->shop_name }}</h3>
    </div>
    

    {{-- Loop through the real products for this shop --}}
    <div class="shop-product-grid">
        @forelse ($products as $product)
        <div class="product-card">
            <a href="{{ route('products.show', $product) }}">
                <div class="product-image-container">
                     @if($product->in_stock < 1)
                <div class="sold-out-overlay">
                    <span>SOLD OUT</span>
                </div>
            @endif
                    {{-- Display the first image from the product's images relationship --}}
                    <img src="{{ asset('storage/' . $product->images->first()?->image_path) }}" alt="{{ $product->product_name }}">
                </div>
            </a>
            <div class="product-info">
                <p class="product-name">{{ $product->product_name }}</p>
                <p class="product-price">${{ number_format($product->product_price) }}</p>
            </div>
        </div>
        @empty
            <div class="empty-state" style="grid-column: 1 / -1; text-align:center;">
                <p>This shop doesn't have any active listings yet.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection