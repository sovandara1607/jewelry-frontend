@extends('layouts.app')

@section('styles')
    {{-- We can reuse the same forms CSS --}}
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endsection

@section('content')
<div class="form-page-container">
     <a href="#" class="page-back-link" id="page-back-link">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        <span>Back</span>
    </a>
    <div class="form-wrapper">
        <h1 class="form-title">Edit Your Shop Page</h1>

        {{-- This will display the success message after an update --}}
        @if (session('status'))
            <p style="color: green; text-align: center; margin-bottom: 20px;">
                {{ session('status') }}
            </p>
        @endif

        <form method="POST" action="{{ route('shops.update') }}">
            @csrf
            @method('PATCH') {{-- Important: Tells Laravel this is an update request --}}

            <div class="form-group">
                <label for="shop_name" class="form-label">Shop Name</label>
                {{-- The value attribute is pre-filled with the current shop name --}}
                <input type="text" id="shop_name" name="shop_name" class="form-control" value="{{ old('shop_name', $shop->shop_name) }}" required>
            </div>

            <div class="form-group">
                <label for="shop_email" class="form-label">Shop Email</label>
                <input type="email" id="shop_email" name="shop_email" class="form-control" value="{{ old('shop_email', $shop->shop_email) }}" required>
            </div>
            
            <div class="form-group">
                <label for="shop_phonenumber" class="form-label">Phone Number</label>
                <input type="tel" id="shop_phonenumber" name="shop_phonenumber" class="form-control" value="{{ old('shop_phonenumber', $shop->shop_phonenumber) }}" required>
            </div>

            <div class="form-group">
                <label for="shop_address" class="form-label">Address</label>
                <input type="text" id="shop_address" name="shop_address" class="form-control" value="{{ old('shop_address', $shop->shop_address) }}" required>
            </div>

            <div class="form-group">
                <label for="shop_description" class="form-label">Shop Description</label>
                <textarea id="shop_description" name="shop_description" class="form-control" rows="5">{{ old('shop_description', $shop->shop_description) }}</textarea>
            </div>
            
            <button type="submit" class="btn-submit">Save Changes</button>
        </form>
    </div>
</div>
@endsection