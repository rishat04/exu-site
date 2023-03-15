<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;


// Route::post('/social/{provider}', 'GoogleAuthController@social');

Route::get('/{vue_capture?}', function() {
    return view('index');
})->where('vue_capture', '[\/\w\.-]*');