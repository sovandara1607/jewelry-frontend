@extends('layouts.app')

@section('styles')
    {{-- We can reuse the auth.css for its centering styles --}}
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

    {{-- 
        THIS IS THE REDIRECT TRICK.
        It tells the browser: "After 3 seconds, go to the page defined by the 'shops.dashboard' route".
        The backend developer will eventually remove this and handle the redirect properly.
    --}}
    <meta http-equiv="refresh" content="3;url={{ route('shops.dashboard') }}">
@endsection

@section('content')
<div class="auth-container">
    <div class="auth-form" style="text-align: center;">
        
        {{-- You can use a simple checkmark icon SVG --}}
        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #28a745; margin-bottom: 20px;">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>

        <h1>Shop Created Successfully!</h1>
        <p style="font-size: 16px; color: #555;">
            You are now being redirected to your shop dashboard.
        </p>
    </div>
</div>
@endsection