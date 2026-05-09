<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get("/", function() {
    return Auth::check()
        ? redirect()->route("dashboard")
        : redirect()->route("login");
});

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login/store', [LoginController::class, 'store'])->name('login.store');
Route::delete('/login', [LoginController::class, 'destroy'])->name('login.destroy');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

//Route::resource('auth', LoginController::class)->only(['create', 'store']);

// authentication pages
//Route::get('/login', function () {
//    return view('pages.auth.login', ['title' => 'Login']);
//})->name('login');






















