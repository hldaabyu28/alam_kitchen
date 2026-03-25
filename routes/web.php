<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuCategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', [LandingController::class, 'index'])->name('landing');


Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register',  [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Public API: validate discount code
Route::post('/api/discount/validate', [DiscountController::class, 'validatePromo'])->name('api.discount.validate');

// Payment Callbacks & Webhooks
Route::post('/payment/midtrans-callback', [PaymentCallbackController::class, 'handleNotification'])->name('payment.callback');
Route::get('/payment/finish', function() {
    return view('landing.payment-finish');
})->name('payment.finish');

// Public order checkout (no auth required)
Route::post('/order', [OrderController::class, 'store'])->name('order.store');

// Public guest reservation (no auth required)
Route::post('/reservation', [ReservationController::class, 'guestStore'])->name('reservation.guest.store');

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
    Route::get('/dashboard', fn() => view('super_admin.dashboard'))->name('dashboard');
    Route::resource('users', UserController::class)->except(['show', 'create', 'edit']);
    Route::resource('menu', MenuController::class)->except(['show', 'create', 'edit']);
    Route::resource('category', MenuCategoryController::class)->except(['show', 'create', 'edit']);
    Route::resource('discount', DiscountController::class)->except(['show', 'create', 'edit']);
    Route::resource('tax', TaxController::class)->except(['show', 'create', 'edit']);
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::patch('/orders/{order}/payment', [OrderController::class, 'updatePayment'])->name('orders.updatePayment');
    Route::resource('table', TableController::class)->except(['show', 'create', 'edit']);

    // Reservasi
    Route::get('/reservasi/available-tables', [ReservationController::class, 'availableTables'])->name('reservasi.availableTables');
    Route::get('/reservasi', [ReservationController::class, 'index'])->name('reservasi.index');
    Route::post('/reservasi', [ReservationController::class, 'store'])->name('reservasi.store');
    Route::get('/reservasi/{reservation}', [ReservationController::class, 'show'])->name('reservasi.show');
    Route::patch('/reservasi/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('reservasi.updateStatus');
    Route::post('/reservasi/{reservation}/order', [ReservationController::class, 'addOrder'])->name('reservasi.addOrder');
});

// Admin
Route::middleware(['auth', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    Route::resource('menu', MenuController::class)->except(['show', 'create', 'edit']);
    Route::resource('category', MenuCategoryController::class)->except(['show', 'create', 'edit']);
    Route::resource('discount', DiscountController::class)->except(['show', 'create', 'edit']);
    Route::resource('tax', TaxController::class)->except(['show', 'create', 'edit']);
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::patch('/orders/{order}/payment', [OrderController::class, 'updatePayment'])->name('orders.updatePayment');
    Route::resource('table', TableController::class)->except(['show', 'create', 'edit']);

    // Reservasi
    Route::get('/reservasi/available-tables', [ReservationController::class, 'availableTables'])->name('reservasi.availableTables');
    Route::get('/reservasi', [ReservationController::class, 'index'])->name('reservasi.index');
    Route::post('/reservasi', [ReservationController::class, 'store'])->name('reservasi.store');
    Route::get('/reservasi/{reservation}', [ReservationController::class, 'show'])->name('reservasi.show');
    Route::patch('/reservasi/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('reservasi.updateStatus');
    Route::post('/reservasi/{reservation}/order', [ReservationController::class, 'addOrder'])->name('reservasi.addOrder');
});

// Kasir
Route::middleware(['auth', 'role:kasir,admin,super_admin'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', fn() => view('kasir.dashboard'))->name('dashboard');
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::get('/transaksi', [TransactionController::class, 'index'])->name('transaksi.index');
    Route::post('/transaksi', [TransactionController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{order}/print', [TransactionController::class, 'print'])->name('transaksi.print');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::patch('/orders/{order}/payment', [OrderController::class, 'updatePayment'])->name('orders.updatePayment');

    // Reservasi
    Route::get('/reservasi/available-tables', [ReservationController::class, 'availableTables'])->name('reservasi.availableTables');
    Route::get('/reservasi', [ReservationController::class, 'index'])->name('reservasi.index');
    Route::post('/reservasi', [ReservationController::class, 'store'])->name('reservasi.store');
    Route::get('/reservasi/{reservation}', [ReservationController::class, 'show'])->name('reservasi.show');
    Route::patch('/reservasi/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('reservasi.updateStatus');
    Route::post('/reservasi/{reservation}/order', [ReservationController::class, 'addOrder'])->name('reservasi.addOrder');
});

/*
|--------------------------------------------------------------------------
| FALLBACK
|--------------------------------------------------------------------------
*/
// Route::redirect('/', '/login');
