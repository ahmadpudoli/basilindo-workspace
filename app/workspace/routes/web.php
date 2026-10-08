<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentDownloadController;
use App\Http\Controllers\DocumentPreviewController;
use App\Http\Controllers\BundleDownloadController;
use App\Http\Controllers\HealthController;
Route::get('health/ready', [HealthController::class, 'ready'])->name('health.ready');

// Compatibility redirects for bookmarks and links from the previous panel URL.
Route::redirect('/admin/login', '/login');
Route::redirect('/admin', '/');

Route::middleware('auth')->group(function () {
    Route::get('/workspace', function () {
        session()->forget('workspace.active_module');

        return redirect('/');
    })->name('workspace.launcher');

    Route::get('/workspace/{module}', function (string $module) {
        $destinations = [
            'core' => '/users',
            'crm' => '/crm-overview',
            'project-management' => '/projects',
            'documents' => '/documents',
        ];

        abort_unless(isset($destinations[$module]), 404);
        session(['workspace.active_module' => $module]);

        return redirect($destinations[$module]);
    })->whereIn('module', ['core', 'crm', 'project-management', 'documents'])
        ->name('workspace.module');
});

Route::middleware('auth')->group(function () {
    Route::get('documents/{document}/download', DocumentDownloadController::class)->name('documents.download');
    Route::get('documents/{document}/preview', DocumentPreviewController::class)->name('documents.preview');
    Route::get('bundles/{bundle}/download', BundleDownloadController::class)->name('bundles.download');
});

