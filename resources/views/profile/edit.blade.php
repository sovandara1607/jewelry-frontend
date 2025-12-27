@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
@endsection

@section('content')
    <div class="form-page-container">
         <a href="#" class="page-back-link" id="page-back-link">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        <span>Back</span>
    </a>
        <div class="form-wrapper">
            <h1 class="form-title">Edit Your Profile</h1>

            {{-- Display a success message if the session has one --}}
            @if (session('status') === 'profile-updated')
                <p style="color: green; margin-bottom: 20px;">Your profile has been updated!</p>
            @endif
            @if (session('status') === 'password-updated')
                <p style="color: green; margin-bottom: 20px;">Your password has been updated!</p>
            @endif
            @if (session('status') === 'avatar-updated')
                <p style="color: green; margin-bottom: 20px;">Your avatar has been updated!</p>
            @endif

            <!-- Form 1: Update Profile Information -->
            <div class="form-section" style="margin-bottom: 40px;">
                <h2 style="font-size: 1.25rem; font-weight: 500; margin-bottom: 30px;">Profile Information</h2>
                <form method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')

                    <div class="form-group">
                        <label for="name" class="form-label">Name</label>
                        <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}"
                            required autofocus>
                        {{-- You'll need to style your error messages if you use them --}}
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="email" class="form-control"
                            value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="phonenumber" class="form-label">Phone Number</label>
                        <input id="phonenumber" name="phonenumber" type="tel" class="form-control"
                            value="{{ old('phonenumber', $user->phonenumber) }}">
                    </div>

                    <!-- Address -->
                    <div class="form-group">
                        <label for="address" class="form-label">Address</label>
                        <input id="address" name="address" type="text" class="form-control"
                            value="{{ old('address', $user->address) }}">
                    </div>
                    <button type="submit" class="btn-submit">Save Changes</button>

                </form>
            </div>


            <!-- Form 2: Update Password -->
            <div class="form-section" style="margin-bottom: 40px;">
                <h2 style="font-size: 1.25rem; font-weight: 500; margin-bottom: 30px;">Update Password</h2>
                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <div class="form-group">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input id="current_password" name="current_password" type="password" class="form-control"
                            autocomplete="current-password">
                        <x-input-error :messages="$errors->get('current_password')" class="form-error" />
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">New Password</label>
                        <input id="password" name="password" type="password" class="form-control"
                            autocomplete="new-password">
                        <x-input-error :messages="$errors->get('password')" class="form-error" />
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirm New Password*</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control"
                            autocomplete="new-password">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="form-error" />
                    </div>

                    <button type="submit" class="btn-submit">Update Password</button>
                </form>
            </div>


            <!-- Form 3: Update Profile Picture -->
            <div class="form-section">
                <h2 style="font-size: 1.25rem; font-weight: 500; margin-bottom: 30px;">Update Profile Picture</h2>
                <form method="post" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="profilepic" class="form-label">Choose a new photo</label>
                        <input id="profilepic" name="profilepic" type="file" class="form-control">
                    </div>

                    <button type="submit" class="btn-submit">Upload Picture</button>
                </form>
            </div>
        </div>
    </div>
@endsection