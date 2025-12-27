@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    <div class="profile-container">
        <h1 class="profile-title">User Profile</h1>
        {{-- This will display the success message after a redirect --}}

        <div class="profile-header">
            {{-- This new container will handle the hover effect --}}
            <div class="avatar-edit-container">
                <div class="profile-avatar">
                    @if (Auth::user()->profilepic)
                        <img src="{{ asset('storage/' . Auth::user()->profilepic) }}"
                            alt="{{ Auth::user()->name }}'s profile picture">
                    @endif
                </div>
                {{-- This is the hover overlay with the pencil icon --}}
                <div class="avatar-edit-overlay">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h9"></path>
                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                    </svg>
                </div>
                {{-- This is a hidden form that the JavaScript will submit --}}
                <form id="avatar-upload-form" action="{{ route('profile.avatar.update') }}" method="POST"
                    enctype="multipart/form-data" style="display: none;">
                    @csrf
                    @method('POST') {{-- Since the route is POST --}}
                    <input type="file" id="avatar-file-input" name="profilepic" accept="image/*">
                </form>
            </div>
            <div class="profile-user-details">
                <h2>{{ $user->name }}</h2>
                <p class="username">{{ $user->email }}</p>
                <!-- <p class="location">Located in {{ $user->location }}</p> -->
            </div>
            <a href="{{ route('profile.edit') }}" class="edit-profile-btn">Edit User Profile</a>
        </div>



        <div class="profile-tabs">
            @if (Auth::user()->shop)
                {{-- If user IS a seller, they only see their purchase history here --}}
                <button class="tab-link active" data-tab="order-processing">Order Processing ({{ $pendingOrders->count() }})</button>
                <button class="tab-link " data-tab="purchase-history">Purchase History ({{ $orders->count() }})</button>
                
            @else
                {{-- If user IS NOT a seller, they see all three tabs --}}
                <button class="tab-link active" data-tab="selling">Selling</button>
                <button class="tab-link" data-tab="order-processing">Order Processing ({{ $pendingOrders->count() }})</button>
                <button class="tab-link" data-tab="purchase-history">Purchase History ({{ $orders->count() }})</button>
            @endif
        </div>

        <!-- Selling Tab Content -->
        {{-- This panel is only active if the user does NOT have a shop --}}
        <div id="selling" class="tab-content {{ Auth::user()->shop ? '' : 'active' }}">
            <div class="empty-state">
                <p>You need a shop page to add listings.</p>
                <a href="{{ route('shops.create') }}" class="btn-dark" style=" display:inline-block;">Create Shop Page</a>
            </div>
        </div>

        <!-- Order Processing Tab Content -->
        <div id="order-processing" class="tab-content {{ Auth::user()->shop ? 'active' : '' }}">
            @forelse ($pendingOrders as $order)
                @foreach ($order->items as $item)
                    <div class="order-card">
                        <img src="{{ asset('storage/' . $item->product->images->first()?->image_path) }}" class="order-image">
                        <div class="order-details">
                            <div class="product-name">{{ $item->product->product_name }}</div>
                            <div class="product-price">${{ number_format($item->price, 2) }}</div>
                            <div class="status-label">Status: Waiting for Seller Confirmation</div>
                        </div>
                    </div>
                @endforeach
            @empty
                <div class="empty-state">
                    <p>You have no orders awaiting confirmation.</p>
                </div>
            @endforelse
        </div>

        <!-- Purchase History Tab Content -->
        {{-- This panel is only active on load if the user HAS a shop --}}
        <div id="purchase-history" class="tab-content">
            @forelse ($orders as $order)
                @foreach ($order->items as $item)
                    <div class="order-card">
                        <img src="{{ asset('storage/' . $item->product->images->first()?->image_path) }}" class="order-image">
                        <div class="order-details">
                            <div class="product-name">{{ $item->product->product_name }}</div>
                            <div class="product-price">${{ number_format($item->price, 2) }}</div>
                            <div class="buyer-info">
                                <span>Purchased from: {{ $item->product->shop->shop_name }}</span>
                                <p>Confirmed on: {{ $order->date_updated->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @empty
                <div class="empty-state">
                    <p>You haven't purchased any items yet.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.tab-link');
            const contents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs and contents
                    tabs.forEach(item => item.classList.remove('active'));
                    contents.forEach(item => item.classList.remove('active'));

                    // Add active class to the clicked tab
                    tab.classList.add('active');

                    // Show the corresponding content
                    const target = document.getElementById(tab.dataset.tab);
                    target.classList.add('active');
                });
            });
        });
    </script>
@endsection