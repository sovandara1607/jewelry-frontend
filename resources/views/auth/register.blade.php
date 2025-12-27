@extends('layouts.app') {{-- Use your main app layout --}}

@section('styles')
    {{-- Link to your custom auth stylesheet --}}
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
    <div class="auth-container">
        <div class="auth-form">
            <h1>Create Account</h1>

            <!-- This form points to Breeze's functional registration route -->
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="form-group">
                    <label for="name" class="form-label">Name</label>
                    {{-- This is a Blade component from Breeze for the input field --}}
                    <x-text-input id="name" class="form-control" type="text" name="name" :value="old('name')" required
                        autofocus autocomplete="name" />
                    {{-- This component displays validation errors for the 'name' field --}}
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required
                        autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Phone Number (Your custom field) -->
                <div class="form-group">
                    <label for="phonenumber" class="form-label">Phone Number</label>
                    <x-text-input id="phonenumber" class="form-control" type="tel" name="phonenumber"
                        :value="old('phonenumber')" required />
                    <x-input-error :messages="$errors->get('phonenumber')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <x-text-input id="password" class="form-control" type="password" name="password" required
                        autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password*</label>
                    <x-text-input id="password_confirmation" class="form-control" type="password"
                        name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="address" class="form-label">Address</label>
                    <input id="address" class="form-control" type="text" name="address" :value="old('address')" placeholder="You can edit this later." required>
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>

                <button type="submit" class="btn-submit">
                    {{ __('Create Account') }}
                </button>
            </form>

            <a class="auth-switch-link" href="{{ route('login') }}">
                {{ __('Already have an account? Log in') }}
            </a>
        </div>
    </div>
@endsection