<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\VideoFormatsController;
use App\Http\Controllers\VideoTrimController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(AuthController::class)
->prefix('auth')
->as('auth.')
->group(function() {
    Route::post('register','register')->name('register');
    Route::post('login', 'login')->name('login');
});

Route::get('/search', SearchController::class);

Route::get('/trim', VideoTrimController::class);
// Route::get('/details', VideoDetailsController::class);
Route::get('/formats', VideoFormatsController::class);
