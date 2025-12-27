<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


//only for mission#7
class AuthController extends Controller
{
    // Method to show the sign up form
    public function showSignupForm()
    {
        return view('auth.signup');
    }

    // You can add a method for the login page later
    public function showLoginForm()
    {
        // For now, it can just redirect to the signup page or show a placeholder
        return view('auth.login'); // We'll create this file next
    }
}