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
use App\Http\Controllers\ContactController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyticsController;
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

// Public contact form submission
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Payment Callbacks & Webhooks
Route::post('/payment/midtrans-callback', [PaymentCallbackController::class, 'handleNotification'])->name('payment.callback');
Route::get('/payment/finish', function () {
    return view('landing.payment-finish');
})->name('payment.finish');

// Public order checkout (no auth required)
Route::post('/order', [OrderController::class, 'store'])->name('order.store');

// Public guest reservation (no auth required)
Route::post('/reservation', [ReservationController::class, 'guestStore'])->name('reservation.guest.store');

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD ROUTES — diproteksi berdasarkan role
|--------------------------------------------------------------------------
*/

// Super Admin
Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super_admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
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

    // Pesan Masuk (Contact CMS)
    Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
    Route::get('/contact/{contact}', [ContactController::class, 'show'])->name('contact.show');
    Route::post('/contact/{contact}/read', [ContactController::class, 'markRead'])->name('contact.read');
    Route::post('/contact/{contact}/unread', [ContactController::class, 'markUnread'])->name('contact.unread');
    Route::delete('/contact/{contact}', [ContactController::class, 'destroy'])->name('contact.destroy');

    // Store Settings
    Route::get('/settings', [StoreController::class, 'index'])->name('settings.index');
    Route::put('/settings', [StoreController::class, 'update'])->name('settings.update');
    Route::post('/settings/faq', [StoreController::class, 'storeFaq'])->name('settings.faq.store');
    Route::put('/settings/faq/{faq}', [StoreController::class, 'updateFaq'])->name('settings.faq.update');
    Route::delete('/settings/faq/{faq}', [StoreController::class, 'destroyFaq'])->name('settings.faq.destroy');
});

// Admin
Route::middleware(['auth', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
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

    // Pesan Masuk (Contact CMS)
    Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
    Route::get('/contact/{contact}', [ContactController::class, 'show'])->name('contact.show');
    Route::post('/contact/{contact}/read', [ContactController::class, 'markRead'])->name('contact.read');
    Route::post('/contact/{contact}/unread', [ContactController::class, 'markUnread'])->name('contact.unread');
    Route::delete('/contact/{contact}', [ContactController::class, 'destroy'])->name('contact.destroy');

    // Store Settings
    Route::get('/settings', [StoreController::class, 'index'])->name('settings.index');
    Route::put('/settings', [StoreController::class, 'update'])->name('settings.update');
    Route::post('/settings/faq', [StoreController::class, 'storeFaq'])->name('settings.faq.store');
    Route::put('/settings/faq/{faq}', [StoreController::class, 'updateFaq'])->name('settings.faq.update');
    Route::delete('/settings/faq/{faq}', [StoreController::class, 'destroyFaq'])->name('settings.faq.destroy');
});

// Kasir
Route::middleware(['auth', 'role:kasir,admin,super_admin'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::get('/transaksi', [TransactionController::class, 'index'])->name('transaksi.index');
    Route::post('/transaksi', [TransactionController::class, 'store'])->name('transaksi.store');
    Route::post('/transaksi/toggle-status', [TransactionController::class, 'toggleStatus'])->name('transaksi.toggle-status');
    Route::get('/transaksi/{order}/print', [TransactionController::class, 'print'])->name('transaksi.print');
    Route::post('/transaksi/{order}/midtrans-success', [TransactionController::class, 'markMidtransSuccess'])->name('transaksi.midtrans-success');
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
