@extends('layouts.app')

{{-- This section injects a page-specific stylesheet --}}
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
@endsection

@section('content')
    <div class="shop-page-container">
        <header class="shop-header">
            <h1>JEWELRIES</h1>
            <p>
                Every item is carefully finished by hand. The jewler uses traditional skills of engraving, piercing,
                fine-soldering and stone mounting. The highest possible standard of craftsmanship is applied to every stage
                of the process. The bead necklaces are all hand-strung, just as every pearl is individually hand-knotted.
            </p>
        </header>

        <form action="{{ route('shop.index') }}" method="GET" id="filter-sort-form">
            <!-- <input type="hidden" name="category" id="category-input" value="{{ request('category') }}"> -->

            <div class="shop-controls">
                <div class="filter-sort-controls">

                    <button type="button" class="filter-btn" id="open-filter-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" y1="21" x2="4" y2="14"></line>
                            <line x1="4" y1="10" x2="4" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12" y2="3"></line>
                            <line x1="20" y1="21" x2="20" y2="16"></line>
                            <line x1="20" y1="12" x2="20" y2="3"></line>
                            <line x1="1" y1="14" x2="7" y2="14"></line>
                            <line x1="9" y1="8" x2="15" y2="8"></line>
                            <line x1="17" y1="16" x2="23" y2="16"></line>
                        </svg>
                        <span>Filters</span>
                    </button>
                    <div class="sort-by">
                        <label for="sort-select">Sort by:</label>
                        <div class="custom-select-wrapper">
                            <select id="sort-select" name="sort" onchange="document.getElementById('filter-sort-form').submit();">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>

                            <svg class="dropdown-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>
            <input type="hidden" name="category" id="category-filter-input" value="{{ request('category') }}">
        </form>

        <div class="shop-product-grid">
            {{-- In the .shop-product-grid loop --}}
            @foreach ($products as $product)
                <div class="product-card product-item" data-category="{{ $product->product_category }}">
                    <a href="{{ route('products.show', ['product' => $product->product_id]) }}" class="product-link">
                        <div class="product-image-container">
                            @if($product->in_stock < 1)
                <div class="sold-out-overlay">
                    <span>SOLD OUT</span>
                </div>
            @endif
                            {{--
                            1. $product->images: Gets the collection of all related images.
                            2. ->first(): Gets only the VERY FIRST image object from that collection.
                            3. ?->image_path: Safely gets the 'image_path' property from that first image object.
                            The '?' prevents an error if a product has no images.
                            --}}
                             @if ($product->images->isNotEmpty())
                            <img src="{{ asset('storage/' . $product->images->first()?->image_path) }}"
                                alt="{{ $product->product_name }}">
                                 @else
                                 <img src="{{ asset('images/placeholder.jpg') }}" alt="No image available">
    @endif

                        </div>
                    </a>
                    <div class="product-info">
                        <p class="product-name">{{ $product->product_name  }}</p>
                        <p class="product-price">${{ number_format($product->product_price) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
   

    <div class="filter-overlay" id="filter-overlay">
        <div class="filter-panel" id="filter-panel">
            <div class="filter-header">
                <h3>Filter by</h3>
                <button class="close-filter-btn" id="close-filter-btn" aria-label="Close filters">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="filter-body">
                <details class="filter-group" open>
                    <summary>
                        <span>Jewelries</span>
                        <svg class="chevron-down" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </summary>
                    <ul class="filter-categories">
                        {{-- Add a class and a data-filter attribute to each list item --}}
                        <li class="filter-link" data-filter="all"><a href="#">All Jewelries</a></li>
                        <li class="filter-link" data-filter="earrings"><a href="#">Earrings</a></li>
                        <li class="filter-link" data-filter="bracelets"><a href="#">Bracelets</a></li>
                        <li class="filter-link" data-filter="rings"><a href="#">Rings</a></li>
                        <li class="filter-link" data-filter="necklaces"><a href="#">Necklaces</a></li>
                        <li class="filter-link" data-filter="other"><a href="#">Other Jewelries</a></li>
                    </ul>
                </details>
            </div>
        </div>
    </div>
     </div>
    {{-- END: FILTER OVERLAY HTML --}}
@endsection

@section('scripts')
<script>
      document.addEventListener('DOMContentLoaded', function () {
        console.log('✅ Script Loaded: The page is ready.');

        // --- 1. LOGIC FOR FILTER OVERLAY ---
        const openBtn = document.getElementById('open-filter-btn');
        const closeBtn = document.getElementById('close-filter-btn');
        const overlay = document.getElementById('filter-overlay');
        const body = document.body;

        // Check if our elements were found
        console.log('🔍 Finding Elements:', { openBtn, closeBtn, overlay });

        function openFilters() {
            console.log('➡️ Opening filters...');
            if (overlay) overlay.classList.add('is-active');
            body.classList.add('overflow-hidden');
        }

        function closeFilters() {
            console.log('⬅️ Closing filters...');
            if (overlay) overlay.classList.remove('is-active');
            body.classList.remove('overflow-hidden');
        }

        // Event listener to open the filter panel
        if (openBtn) {
            console.log('👍 Open button found. Attaching listener.');
            openBtn.addEventListener('click', function (event) {
                console.log('🔴 Filter button was CLICKED!');
                event.preventDefault(); 
                openFilters();
            });
        } else {
            console.error('❌ ERROR: Could not find the open button with id="open-filter-btn"');
        }

        // Event listener to close the filter panel with the 'X' button
        if (closeBtn) {
            closeBtn.addEventListener('click', closeFilters);
        }

        // Event listener to close the filter panel by clicking on the background
        if (overlay) {
            overlay.addEventListener('click', function (event) {
                if (event.target === overlay) {
                    closeFilters();
                }
            });
        }


        // --- 2. LOGIC FOR SERVER-SIDE FILTERING & SORTING ---
        const filterSortForm = document.getElementById('filter-sort-form');
        const categoryInput = document.getElementById('category-filter-input');
        const filterLinks = document.querySelectorAll('.filter-link');

        filterLinks.forEach(link => {
            link.addEventListener('click', function (event) {
                event.preventDefault(); // Stop the link from navigating normally

                // Get the category to filter by from the link's data attribute
                const filterValue = this.dataset.filter;

                // Set the value of the hidden category input field
                if (categoryInput) {
                    categoryInput.value = filterValue;
                }

                // Submit the main form to apply filters and sorting
                if (filterSortForm) {
                    filterSortForm.submit();
                }
            });
        });
        

        // --- 3. LOGIC FOR SCROLL POSITION RESTORATION ---
        const productLinks = document.querySelectorAll('a.product-link');
        
        // Save scroll position when a user clicks on a product
        productLinks.forEach(link => {
            link.addEventListener('click', function () {
                sessionStorage.setItem('shopScrollY', window.scrollY);
            });
        });

        // Restore scroll position when the user navigates back to this page
        const scrollY = sessionStorage.getItem('shopScrollY');
        if (scrollY) {
            window.scrollTo(0, parseInt(scrollY, 10));
            sessionStorage.removeItem('shopScrollY'); // Clean up the session storage
        }
    });
</script>
@endsection