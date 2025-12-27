@extends('layouts.app')


@section('content')
    <div class="product-page-container">
        <div class="breadcrumbs">
            <a href="{{ route('home') }}">Home</a> /
            <a href="{{ route('shop.index') }}">Jewelries</a> /
            <a
                href="{{ route('shop.index') }}?category={{ strtolower($product->product_category) }}">{{ ucfirst($product->product_category) }}</a>
            /
            <span>{{ $product->product_name }}</span>
        </div>

        <a href="#" class="back-link" id="back-link">

            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>


        <div class="product-layout">
            <div class="image-gallery">
                <div class="thumbnail-list">
                    @foreach($product->images as $image)
                        <div class="thumbnail-item" data-src="{{ asset('storage/' . $image->image_path) }}">
                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                alt="Thumbnail of {{ $product->product_name }}">
                        </div>
                    @endforeach
                </div>
                <div class="main-image-container">
                    {{-- Display the first image by default, with a null-safe check --}}
                    <img id="main-product-image" src="{{ asset('storage/' . $product->images->first()?->image_path) }}"
                        alt="{{ $product->product_name }}">
                    <button class="gallery-arrow prev" aria-label="Previous image">←</button>
                    <button class="gallery-arrow next" aria-label="Next image">→</button>
                </div>
            </div>

            <div class="product-details">
                <div class="product-title-header">
                    <h1>{{ $product->product_name }}</h1>
                    {{-- We check if the user is the owner of the product --}}
                    @if (Auth::check() && Auth::user()->shop?->shop_id == $product->shop_id)

                        {{-- USER OWNS THIS PRODUCT: Show the new kebab menu --}}
                        <div class="product-actions-dropdown">
                            <button class="kebab-toggle" aria-label="Product options">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="1"></circle>
                                    <circle cx="12" cy="5" r="1"></circle>
                                    <circle cx="12" cy="19" r="1"></circle>
                                </svg>
                            </button>
                            <div class="kebab-menu">
                                <a href="{{ route('products.edit', $product) }}" class="kebab-item">Edit Listing</a>

                                {{-- This is a form for the delete action --}}
                                <form action="{{ route('products.destroy', $product) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to permanently delete this listing?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="kebab-item danger">Delete Listing</button>
                                </form>
                            </div>
                        </div>
                    @endif

                </div>
                <p class="product-price">${{ number_format($product->product_price, 2) }}</p>
                <p class="product-stock">
                    @if ($product->in_stock > 0)
                        Only 1 available
                    @else
                        Sold Out
                    @endif
                </p>


                {{-- The "Add to Bag" button is now separate from the kebab menu logic --}}
                @if (!Auth::check() || Auth::user()->shop?->shop_id != $product->shop_id)
                    @if ($product->in_stock > 0)

                        {{-- PRODUCT IS IN STOCK: Show the functional "Add to Bag" button --}}
                        <form action="{{ route('cart.add', $product) }}" method="POST">
                            @csrf
                            <button type="submit" class="add-to-bag-btn">Add to Bag</button>
                        </form>

                    @else

                        {{-- PRODUCT IS SOLD OUT: Show a disabled "Sold Out" button --}}
                        <button type="button" class="add-to-bag-btn sold-out-btn" disabled>
                            Sold Out
                        </button>

                    @endif

                @endif


                <div class="description-accordion">
                    <details open>
                        <summary>DESCRIPTION</summary>
                        <div class="description-content">
                            <p>{{ $product->product_description }}</p>

                        </div>
                    </details>
                </div>

                <p class="product-id">Product ID: {{ $product->product_id }}</p>

                @if ($seller)
                    <a href="{{ route('shops.public', ['handle' => $seller->shop_name]) }}" class="seller-info-card-link">
                        <div class="seller-info-card">
                            <div class="seller-avatar">
                                @if ($seller->shop_profilepic)
                                    <img src="{{ asset('storage/' . $seller->shop_profilepic) }}"
                                        alt="{{ $seller->shop_name }}'s logo">
                                @endif
                            </div>
                            <div class="seller-details">
                                <div class="shop-name">{{ $seller->shop_name }}</div>
                                <div class="handle">{{ $seller->shop_email }}</div>
                                <div class="location">Located in {{ $seller->shop_address }}</div>
                            </div>
                        </div>
                    </a>
                @endif

            </div>
        </div>
    </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // --- 1. LOGIC FOR THE MAIN "BACK" ARROW ---
            const pageBackButton = document.getElementById('back-link');
            if (pageBackButton) {
                pageBackButton.addEventListener('click', function (event) {
                    event.preventDefault(); // Stop the link from navigating
                    history.back(); // Use browser history to go back to the previous page
                });
            }

            // --- 2. LOGIC FOR THE IMAGE GALLERY ---
            const mainImage = document.getElementById('main-product-image');
            const thumbnails = document.querySelectorAll('.thumbnail-item');
            const prevArrow = document.querySelector('.gallery-arrow.prev');
            const nextArrow = document.querySelector('.gallery-arrow.next');
            let currentImageIndex = 0;

            // A function to update the main image and active thumbnail
            function setActiveImage(index) {
                if (index < 0) {
                    index = thumbnails.length - 1; // Loop to the end
                } else if (index >= thumbnails.length) {
                    index = 0; // Loop to the beginning
                }

                // Update the main image source
                if (thumbnails[index]) {
                    mainImage.src = thumbnails[index].dataset.src;
                }

                // Update which thumbnail has the 'active' class
                thumbnails.forEach(thumb => thumb.classList.remove('active'));
                if (thumbnails[index]) {
                    thumbnails[index].classList.add('active');
                }

                currentImageIndex = index;
            }

            // Add click listeners to each thumbnail
            thumbnails.forEach((thumbnail, index) => {
                thumbnail.addEventListener('click', () => {
                    setActiveImage(index);
                });
            });

            // Add click listener for the "previous" arrow on the main image
            if (prevArrow) {
                prevArrow.addEventListener('click', () => {
                    setActiveImage(currentImageIndex - 1);
                });
            }

            // Add click listener for the "next" arrow on the main image
            if (nextArrow) {
                nextArrow.addEventListener('click', () => {
                    setActiveImage(currentImageIndex + 1);
                });
            }

            // Set the first image as active when the page loads
            if (thumbnails.length > 0) {
                setActiveImage(0);
            }

            // --- NEW SCRIPT FOR KEBAB DROPDOWN ---
            const kebabToggle = document.querySelector('.kebab-toggle');
            const kebabMenu = document.querySelector('.kebab-menu');

            if (kebabToggle && kebabMenu) {
                kebabToggle.addEventListener('click', function (event) {
                    event.stopPropagation();
                    kebabMenu.classList.toggle('is-active');
                });

                // Close the dropdown if clicking elsewhere
                document.addEventListener('click', function () {
                    if (kebabMenu.classList.contains('is-active')) {
                        kebabMenu.classList.remove('is-active');
                    }
                });
            }
        });
    </script>
@endsection