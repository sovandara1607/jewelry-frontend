<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Handmade Jewelries</title> <!-- Changed title -->

    <!-- Fonts (Using your custom fonts) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500&display=swap"
        rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Link to all your custom CSS files --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <link rel="stylesheet" href="{{ asset('css/productdetail.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ordersuccess.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sellerpage.css') }}">

</head>

<body class="font-sans antialiased">
    {{-- We removed the Breeze outer div to allow for full-width sections --}}

    {{-- This is YOUR custom header --}}
    <header class="site-header-wrapper">
        <div class="container">
            @include('partials.header')
        </div>
    </header>

    <!-- Page Heading (This is optional from Breeze, can be removed if you don't use it) -->
    @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <!-- Page Content -->
    <main>
        {{-- Changed from {{ $slot }} to @yield('content') --}}
        @yield('content')
    </main>

    {{-- Add your custom footer back in --}}
    @include('partials.footer')

    @auth
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const userFileInput = document.getElementById('avatar-file-input');
                const userUploadForm = document.getElementById('avatar-upload-form');
                if (userFileInput && userUploadForm) {
                    userFileInput.addEventListener('change', () => {
                        if (userFileInput.files.length > 0) {
                            userUploadForm.submit();
                        }
                    });
                }

                const shopFileInput = document.getElementById('shop-file-input');
                const shopUploadForm = document.getElementById('shop-picture-upload-form');
                if (shopFileInput && shopUploadForm) {
                    shopFileInput.addEventListener('change', () => {
                        if (shopFileInput.files.length > 0) {
                            shopUploadForm.submit();
                        }
                    });
                }
                // --- ADD THIS LOGIC FOR THE GLOBAL BACK BUTTON ---
                const pageBackButton = document.getElementById('page-back-link');
                if (pageBackButton) {
                    pageBackButton.addEventListener('click', function (event) {
                        event.preventDefault(); // Stop the link from navigating
                        history.back(); // Tell the browser to go to the previous page
                    });
                }
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // --- Logic for User Profile Avatar ---
                const userAvatarContainer = document.querySelector('#avatar-upload-form')?.parentElement;
                const userFileInput = document.getElementById('avatar-file-input');
                const userUploadForm = document.getElementById('avatar-upload-form');

                if (userAvatarContainer && userFileInput && userUploadForm) {
                    // When the user clicks the avatar container, trigger the hidden file input
                    userAvatarContainer.addEventListener('click', () => {
                        userFileInput.click();
                    });

                    // When the user selects a file, automatically submit the form
                    userFileInput.addEventListener('change', () => {
                        if (userFileInput.files.length > 0) {
                            userUploadForm.submit();
                        }
                    });
                }

                // --- Logic for Shop Profile Picture ---
                const shopAvatarContainer = document.querySelector('#shop-picture-upload-form')?.parentElement;
                const shopFileInput = document.getElementById('shop-file-input');
                const shopUploadForm = document.getElementById('shop-picture-upload-form');

                if (shopAvatarContainer && shopFileInput && shopUploadForm) {
                    shopAvatarContainer.addEventListener('click', () => {
                        shopFileInput.click();
                    });

                    shopFileInput.addEventListener('change', () => {
                        if (shopFileInput.files.length > 0) {
                            shopUploadForm.submit();
                        }
                    });
                }
            });
        </script>
    @endauth
    @yield('scripts')
</body>

</html>