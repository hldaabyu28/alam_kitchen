<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuCategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register',  [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/' , [LandingController::class, 'index'])->name('landing');
});

// Public order checkout (no auth required)
Route::post('/order', [OrderController::class, 'store'])->name('order.store');

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| DASHBOARD ROUTES — diproteksi berdasarkan role
|--------------------------------------------------------------------------
*/

// Super Admin
Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super_admin.')->group(function () {
    Route::get('/dashboard', fn () => view('super_admin.dashboard'))->name('dashboard');
    Route::resource('menu', MenuController::class)->except(['show', 'create', 'edit']);
    Route::resource('category', MenuCategoryController::class)->except(['show', 'create', 'edit']);
    Route::resource('discount', DiscountController::class)->except(['show', 'create', 'edit']);
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
});

// Admin
Route::middleware(['auth', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');
    Route::resource('menu', MenuController::class)->except(['show', 'create', 'edit']);
    Route::resource('category', MenuCategoryController::class)->except(['show', 'create', 'edit']);
    Route::resource('discount', DiscountController::class)->except(['show', 'create', 'edit']);
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
});

// Kasir
Route::middleware(['auth', 'role:kasir,admin,super_admin'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', fn () => view('kasir.dashboard'))->name('dashboard');
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::get('/transaksi', [TransactionController::class, 'index'])->name('transaksi.index');
    Route::post('/transaksi', [TransactionController::class, 'store'])->name('transaksi.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
});

/*
|--------------------------------------------------------------------------
| FALLBACK
|--------------------------------------------------------------------------
*/
// Route::redirect('/', '/login');

