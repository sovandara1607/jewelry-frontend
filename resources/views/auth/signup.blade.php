@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="auth-container">
    <div class="auth-form">
        <h1>Create Account</h1>

        <form action="#" method="POST">
            {{-- CSRF token for security - backend will need this --}}
            @csrf 

            <div class="form-group">
                <label for="username" class="form-label">Username*</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email*</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">Phone Number*</label>
                <input type="tel" id="phone" name="phone_number" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="address" class="form-label">Address*</label>
                <input type="text" id="address" name="address" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password*</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn-submit">Create Account</button>
        </form>

        <a href="{{ route('login') }}" class="auth-switch-link">Already have an account? Log in</a>
    </div>
</div>
@endsection