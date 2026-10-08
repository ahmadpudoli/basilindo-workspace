<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentDownloadController;
use App\Http\Controllers\DocumentPreviewController;
use App\Http\Controllers\BundleDownloadController;
use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/admin');
    }

    return view('basilindo-welcome');
});

Route::get('health/ready', [HealthController::class, 'ready'])->name('health.ready');

Route::middleware('auth')->group(function () {
    Route::get('documents/{document}/download', DocumentDownloadController::class)->name('documents.download');
    Route::get('documents/{document}/preview', DocumentPreviewController::class)->name('documents.preview');
    Route::get('bundles/{bundle}/download', BundleDownloadController::class)->name('bundles.download');
});

