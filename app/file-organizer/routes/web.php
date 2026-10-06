<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\SsoController;
use App\Http\Controllers\DocumentDownloadController;
use App\Http\Controllers\BundleDownloadController;

Route::get('/', function () {
    return view('basilindo-welcome');
});

// Google Authentication Routes
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('auth/sso', [SsoController::class, 'redirect'])->name('auth.sso');
Route::get('auth/sso/callback', [SsoController::class, 'callback'])->name('auth.sso.callback');
Route::get('auth/sso/logout', [SsoController::class, 'logout'])->name('auth.sso.logout');
Route::middleware('auth')->group(function () {
    Route::get('documents/{document}/download', DocumentDownloadController::class)->name('documents.download');
    Route::get('bundles/{bundle}/download', BundleDownloadController::class)->name('bundles.download');
});

