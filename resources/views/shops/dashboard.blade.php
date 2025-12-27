@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    <div class="profile-container">
        <h1 class="profile-title">Shop Page</h1>

        <div class="profile-header">
            <div class="avatar-edit-container">
                <div class="profile-avatar">
                    @if ($shop->shop_profilepic)
                        <img src="{{ asset('storage/' . $shop->shop_profilepic) }}" alt="{{ $shop->shop_name }}'s logo">
                    @endif
                </div>
                <div class="avatar-edit-overlay">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h9"></path>
                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                    </svg>
                </div>
                <form id="shop-picture-upload-form" action="{{ route('shops.picture.update') }}" method="POST"
                    enctype="multipart/form-data" style="display: none;">
                    @csrf
                    @method('PATCH') {{-- Match the route method --}}
                    <input type="file" id="shop-file-input" name="shop_profilepic" accept="image/*">
                </form>
            </div>
            <div class="profile-user-details">
                <h2>{{ $shop->shop_name }}</h2>
                <p class="username"> {{ Auth::user()->shop->shop_email }}</p>
                <p class="location">Located in {{ $shop->shop_address }}</p>
            </div>
            <a href="{{ route('shops.edit') }}" class="edit-profile-btn">Edit Shop Page</a>
        </div>

        <div class="shop-description-section">
            {{-- Check if a description exists before trying to display it --}}
            @if ($shop->shop_description)
                <p>{{ $shop->shop_description }}</p>
            @else
                {{-- Optional: Show a message if the description is empty --}}
                <p class="text-muted">This shop doesn't have a description yet. <a href="{{ route('shops.edit') }}">Add one
                        now</a>.</p>
            @endif
        </div>

        <div class="profile-tabs">
            <button class="tab-link active" data-tab="selling">Selling ({{ $listings->count() }})</button>
            <button class="tab-link" data-tab="order-processing">Order Processing ({{ $pendingOrders->count() }})</button>
            <button class="tab-link" data-tab="order-history">Order History ({{ $confirmedOrders->count() }})</button>
        </div>

        <!-- Tab 1: Selling (Active Listings) -->
        {{-- This is the only panel that should have 'active' on page load --}}
        <div id="selling" class="tab-content active">
            @if ($listings->isNotEmpty())
                <div class="shop-product-grid">
                    @foreach ($listings as $product)
                        <div class="product-card">
                            <a href="{{ route('products.show', $product) }}">
                                <div class="product-image-container">
                                    <img src="{{ asset('storage/' . $product->images->first()?->image_path) }}"
                                        alt="{{ $product->product_name }}">
                                </div>
                            </a>
                            <div class="product-info">
                                <p class="product-name">{{ $product->product_name }}</p>
                                <p class="product-price">${{ number_format($product->product_price) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                {{-- Show the button BELOW the grid if there are listings --}}
        <div style="text-align: center; margin-top: 40px;">
            <a href="{{ route('products.create') }}" class="btn-dark">+ Add another listing</a>
        </div>
            @else
                <div class="empty-state">
                    <p>You don't have any listings for sale.</p>
                    <div style="text-align: center; margin-top: 20px;">
                <a href="{{ route('products.create') }}" class="btn-dark">+ Start a new listing</a>
            </div>
                </div>
            @endif
            
        </div>

        <!-- Tab 2: Order Processing (Pending Orders) -->
        {{-- This panel is hidden by default (no 'active' class) --}}
        <div id="order-processing" class="tab-content">
            @forelse ($pendingOrders as $orderItem)
                <div class="order-card">
                    <img src="{{ asset('storage/' . $orderItem->product->images->first()?->image_path) }}" class="order-image">
                    <div class="order-details">
                        <div class="product-name">{{ $orderItem->product->product_name }}</div>
                        <div class="product-price">${{ number_format($orderItem->price, 2) }}</div>
                        <div class="buyer-info">
                            <span>Buyer: {{ $orderItem->order->user->name }}</span> | <span>Contact:
                                {{ $orderItem->order->user->email }}</span>
                            <p>Address: {{ $orderItem->order->delivery_address }}</p>
                        </div>
                    </div>
                    <div class="order-actions">
                        <form action="{{ route('orders.accept', $orderItem) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-accept">Accept Order</button>
                        </form>
                        <form action="{{ route('orders.reject', $orderItem) }}" method="POST" style="margin-top: 10px;">
                            @csrf
                            <button type="submit" class="btn-reject">Reject Order</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <p>You have no pending orders.</p>
                </div>
            @endforelse
        </div>

        <!-- Tab 3: Order History (Confirmed Orders) -->
        {{-- This panel is hidden by default (no 'active' class) --}}
        <div id="order-history" class="tab-content">
            @forelse ($confirmedOrders as $orderItem)
                <div class="order-card">
                    <img src="{{ asset('storage/' . $orderItem->product->images->first()?->image_path) }}" class="order-image">
                    <div class="order-details">
                        <div class="product-name">{{ $orderItem->product->product_name }} (SOLD)</div>
                        <div class="product-price">${{ number_format($orderItem->price, 2) }}</div>
                        <div class="buyer-info">
                            <span>Sold to: {{ $orderItem->order->user->name }}</span>
                            <p>Confirmed on: {{ $orderItem->order->date_updated->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <p>You have no completed orders.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection


@section('scripts')
    {{-- We can reuse the same tab-switching script from the profile page! --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.tab-link');
            const contents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(item => item.classList.remove('active'));
                    contents.forEach(item => item.classList.remove('active'));
                    tab.classList.add('active');
                    const target = document.getElementById(tab.dataset.tab);
                    target.classList.add('active');
                });
            });
        });
    </script>
@endsection