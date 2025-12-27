@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/ordersuccess.css') }}">
@endsection

@section('content')
<div class="success-container">
    <div class="success-message">
        <h1>Your order was successful,<br>Thank you for shopping with Handmade.</h1>
    </div>
    <div class="back-to-home-prompt">
        Back to Home Page?
    </div>
    <a href="{{ route('home') }}" class="home-btn">Home</a>
</div>
@endsection