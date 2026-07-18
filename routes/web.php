<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Page\DriverController;
use Illuminate\Support\Facades\Route;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// Auth
Route::get("/", function() {
    return Auth::check()
        ? redirect()->route("dashboard")
        : redirect()->route("login");
});

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login/store', [LoginController::class, 'store'])->name('login.store');
Route::delete('/login', [LoginController::class, 'destroy'])->name('login.destroy');
Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Documents
Route::get('/driver-documents/{media}', function (Media $media) {
    abort_unless($media->model_type === \App\Models\Driver::class, 404);

    /** @var \App\Models\Driver $driver */
    $driver = $media->model;

    abort_unless(auth()->user()?->can('view', $driver), 403);

    return response()->file($media->getPath());
})->middleware('auth')->name('driver-documents.show');

// After login
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('/drivers', DriverController::class)->only(['index', 'edit', 'create']);
});
