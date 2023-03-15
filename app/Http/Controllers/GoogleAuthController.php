<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function social($provider) {
        $auth = Socialite::driver($provider)->stateless()->user();
        $email = $auth->getEmail();
        $user = User::where('email', $email);
    }
}
