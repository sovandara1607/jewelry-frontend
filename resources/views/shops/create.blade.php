@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endsection

@section('content')
    <div class="form-page-container">
        <div class="form-wrapper">
            <h1 class="form-title">Create a New Shop Page</h1>

            {{-- This now points to a new 'store' route we will create --}}
            <form action="{{ route('shops.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="shop_name" class="form-label">Shop Name</label>
                    {{-- Add class="form-control" --}}
                    <input type="text" id="shop_name" name="shop_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="shop_email" class="form-label">Shop Email</label>
                    {{-- Add class="form-control" --}}
                    <input type="email" id="shop_email" name="shop_email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="shop_phonenumber" class="form-label">Phone Number</label>
                    {{-- Add class="form-control" --}}
                    <input type="tel" id="shop_phonenumber" name="shop_phonenumber" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="shop_address" class="form-label">Address</label>
                    {{-- Add class="form-control" --}}
                    <input type="text" id="shop_address" name="shop_address" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="shop_description" class="form-label">Shop Description</label>
                    {{-- Add class="form-control" --}}
                    <textarea id="shop_description" name="shop_description" class="form-control" rows="4"
                        placeholder="Tell customers a little about your shop..."></textarea>
                </div>

                <button type="submit" class="btn-submit">Create Shop</button>
            </form>

        </div>
    </div>
@endsection