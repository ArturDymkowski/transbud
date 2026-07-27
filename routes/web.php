<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Page\ContractorAddressController;
use App\Http\Controllers\Page\ContractorController;
use App\Http\Controllers\Page\DriverController;
use App\Http\Controllers\Page\GoodController;
use App\Http\Controllers\Page\PermissionController;
use App\Http\Controllers\Page\RoleController;
use App\Http\Controllers\Page\UnitController;
use App\Http\Controllers\Page\UserController;
use App\Http\Controllers\Page\VehicleController;
use App\Models\Driver;
use Illuminate\Support\Facades\Route;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// Auth
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login/store', [LoginController::class, 'store'])->name('login.store');
Route::delete('/login', [LoginController::class, 'destroy'])->name('login.destroy');
Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Documents
Route::get('/driver-documents/{media}', function (Media $media) {
    abort_unless($media->model_type === Driver::class, 404);

    return response()->file($media->getPath());
})->middleware('auth')->name('driver-documents.show');

// After login
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('/drivers', DriverController::class)->only(['index', 'edit', 'create', 'show'])
        ->middlewareFor(['index', 'show'], 'can:drivers.view')
        ->middlewareFor('create', 'can:drivers.create')
        ->middlewareFor('edit', 'can:drivers.edit');

    Route::resource('/vehicles', VehicleController::class)->only(['index', 'edit', 'create', 'show'])
        ->middlewareFor(['index', 'show'], 'can:vehicles.view')
        ->middlewareFor('create', 'can:vehicles.create')
        ->middlewareFor('edit', 'can:vehicles.edit');

    Route::resource('/contractors', ContractorController::class)->only(['index', 'edit', 'create', 'show'])
        ->middlewareFor(['index', 'show'], 'can:contractors.view')
        ->middlewareFor('create', 'can:contractors.create')
        ->middlewareFor('edit', 'can:contractors.edit');

    Route::resource('/contractor-addresses', ContractorAddressController::class)->only(['index', 'edit', 'create', 'show'])
        ->middlewareFor(['index', 'show'], 'can:contractor-addresses.view')
        ->middlewareFor('create', 'can:contractor-addresses.create')
        ->middlewareFor('edit', 'can:contractor-addresses.edit');

    Route::resource('/goods', GoodController::class)->only(['index', 'edit', 'create', 'show'])
        ->middlewareFor(['index', 'show'], 'can:goods.view')
        ->middlewareFor('create', 'can:goods.create')
        ->middlewareFor('edit', 'can:goods.edit');

    Route::resource('/units', UnitController::class)->only(['index', 'edit', 'create', 'show'])
        ->middlewareFor(['index', 'show'], 'can:units.view')
        ->middlewareFor('create', 'can:units.create')
        ->middlewareFor('edit', 'can:units.edit');

    Route::resource('/users', UserController::class)->only(['index', 'edit', 'create', 'show'])
        ->middlewareFor(['index', 'show'], 'can:users.view')
        ->middlewareFor('create', 'can:users.create')
        ->middlewareFor('edit', 'can:users.edit');

    Route::resource('/roles', RoleController::class)->only(['index', 'edit', 'create'])
        ->middlewareFor('index', 'can:roles.view')
        ->middlewareFor('create', 'can:roles.create')
        ->middlewareFor('edit', 'can:roles.edit');

    Route::resource('/permissions', PermissionController::class)->only(['index', 'edit'])
        ->middlewareFor('index', 'can:permissions.view')
        ->middlewareFor('edit', 'can:permissions.edit');
});
