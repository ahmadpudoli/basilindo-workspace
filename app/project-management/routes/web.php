<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ExternalLogin;
use App\Livewire\ExternalDashboard;
use App\Http\Controllers\Auth\SsoController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/admin');
    }

    return view('welcome');
});

Route::get('auth/sso', [SsoController::class, 'redirect'])->name('auth.sso');
Route::get('auth/sso/callback', [SsoController::class, 'callback'])->name('auth.sso.callback');
Route::get('auth/sso/logout', [SsoController::class, 'logout'])->name('auth.sso.logout');

// External Dashboard Routes
Route::prefix('external')->name('external.')->group(function () {
    Route::get('/{token}', ExternalLogin::class)->name('login');
    Route::get('/{token}/dashboard', ExternalDashboard::class)->name('dashboard');
});
