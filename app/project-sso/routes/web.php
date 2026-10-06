<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ExternalLogin;
use App\Livewire\ExternalDashboard;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\OidcController;
use App\Http\Controllers\OidcLogoutController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', function () {
    if ($target = session()->pull('oauth_authorize_url')) {
        session()->put('url.intended', $target);
    }

    return redirect()->route('filament.admin.auth.login');
})->name('login');

Route::get('.well-known/openid-configuration', [OidcController::class, 'discovery'])->name('oidc.discovery');
Route::middleware('auth:api')->get('oidc/userinfo', [OidcController::class, 'userinfo'])->name('oidc.userinfo');
Route::get('oidc/jwks', [OidcController::class, 'jwks'])->name('oidc.jwks');
Route::get('oidc/logout', OidcLogoutController::class)->name('oidc.logout');

// Google Authentication Routes
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// External Dashboard Routes
Route::prefix('external')->name('external.')->group(function () {
    Route::get('/{token}', ExternalLogin::class)->name('login');
    Route::get('/{token}/dashboard', ExternalDashboard::class)->name('dashboard');
});
