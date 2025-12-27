@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
    $isGuest = !Auth::check();
    $hasShop = $user && $user->shop;
@endphp

@section('content')
    <!-- Hero Section (Full width background, centered content) -->
    <section class="hero-section" style="background-image: url('{{ asset('images/herobg.jpg') }}');">
        <div class="section-container">
            <div class="hero-content">
                <h1>Handmade Jewelries,<br>Made To Tell A Story.</h1>
                <a href="{{ route('shop.index') }}" class="btn-shop">Shop Now →</a>
            </div>
        </div>
    </section>

    <!-- Newest Arrivals Section -->
    <section class="new-arrivals-section">
        <div class="section-container">
            <h2 class="section-title">
                <a href="{{ route('shop.index') }}">Newest Arrivals ›</a>
            </h2>
            <div class="product-grid">
                @foreach ($products as $product)
                    <div class="product-card">
                        {{-- This link points to the 'products.show' route and passes the product's ID --}}
                        <a href="{{ route('products.show', $product) }}" class="product-link">
                            <div class="product-image-container">
                                {{-- We get the first image from the collection to use as a thumbnail --}}
                                <img src="{{ asset('storage/' . $product->images->first()?->image_path) }}"
                                    alt="{{ $product->product_name }}">
                            </div>
                            <div class="product-info">
                                <p class="product-name">{{ $product->product_name }}</p>
                                <p class="product-price">${{ number_format($product->product_price) }}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Rings Collection Section (This one is special as it's a grid) -->
    <section class="collection-section">
        <div class="collection-image" style="background-image: url('{{ asset('images/ringsbg.jpg') }}');">
        </div>
        <div class="collection-content">
            <h2 class="collection-title">Rings</h2>
            <p class="collection-description">
                Explore Handmade Collection of handcrafted earrings, necklaces and rings created by global artisans. From
                simple silver rings to beautifully gold bracelets, you will find designs in an array of metal and colors to
                suit your personal style.
            </p>
            <a href="{{ route('shop.index') }}" class="btn-shop">Shop Now →</a>
        </div>
    </section>

    <!-- Marketplace Intro Section -->
    <section class="marketplace-intro-section" style="background-image: url('{{ asset('images/marketplacebg.jpg') }}');">
        <div class="section-container">
            <div class="intro-content">
                <h2 style="color:black;"><strong>Welcome to the<br>marketplace made<br>for all of us</strong></h2>
                <p style="color:white;">We believe in goods with timeless quality, especially at a time when automation is
                    at odds with genuine creativity. That's why we only offer items with rich stories that help you tell
                    your own story. While giving makers on our marketplace the tools to make a living.</p>
                <a href="{{ route('shop.index') }}" class="btn-shop">Shop Now →</a>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section">
        <div class="section-container" style="display: flex; justify-content: center; gap: 40px;">
            <div class="cta-card">
                <h3>Sellers</h3>
                <p>Join today to advocate for a handmade future.</p>
                <a href="{{ $isGuest ? route('register') : ($hasShop ? route('shops.dashboard') : route('shops.create')) }}"
                    class="btn-dark">
                    Become a Seller
                </a>
            </div>
            <div class="cta-card">
                <h3>Buyer</h3>
                <p>Join today to advocate for a handmade future.</p>
                <a href="{{ $isGuest ? route('register') : route('shop.index') }}" class="btn-dark">
                    Become a Buyer
                </a>
            </div>
        </div>
    </section>

@endsection