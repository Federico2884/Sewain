<?php

use App\Http\Controllers\Auth\VendorAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\User\ChatController as UserChatController;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\User\RentalController as UserRentalController;
use App\Http\Controllers\User\ReviewController as UserReviewController;
use App\Http\Controllers\Vendor\ChatController as VendorChatController;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboard;
use App\Http\Controllers\Vendor\ItemController as VendorItemController;
use App\Http\Controllers\Vendor\RentalController as VendorRentalController;
use App\Http\Controllers\Vendor\ReturnController;
use App\Http\Controllers\Vendor\ReviewController as VendorReviewController;
use Illuminate\Support\Facades\Route;

// ── Public ─────────────────────────────────────────────────────────────────────

Route::get('/', HomeController::class)->name('home');

Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

// ── Vendor Auth ────────────────────────────────────────────────────────────────

Route::middleware('redirect_if_vendor')->group(function () {
    Route::get('/vendor/register', [VendorAuthController::class, 'registerForm'])->name('vendor.register');
    Route::post('/vendor/register', [VendorAuthController::class, 'register']);
    Route::get('/vendor/login', [VendorAuthController::class, 'loginForm'])->name('vendor.login');
    Route::post('/vendor/login', [VendorAuthController::class, 'login']);
});

Route::post('/vendor/logout', [VendorAuthController::class, 'logout'])
    ->name('vendor.logout')
    ->middleware('ensure_is_vendor');

// ── Vendor Panel ───────────────────────────────────────────────────────────────

Route::prefix('vendor')
    ->name('vendor.')
    ->middleware('ensure_is_vendor')
    ->group(function () {
        Route::get('/dashboard', VendorDashboard::class)->name('dashboard');

        // Items management
        Route::resource('items', VendorItemController::class);
        Route::patch('items/{item}/availability', [VendorItemController::class, 'toggleAvailability'])
            ->name('items.toggle-availability');

        // Rentals overview
        Route::resource('rentals', VendorRentalController::class)->only(['index', 'show']);

        // Return verification
        Route::post('rentals/{rental}/return/verify', [ReturnController::class, 'verify'])
            ->name('rentals.return.verify');

        // Review renter
        Route::get('rentals/{rental}/review/create', [VendorReviewController::class, 'create'])
            ->name('rentals.review.create');
        Route::post('rentals/{rental}/review', [VendorReviewController::class, 'store'])
            ->name('rentals.review.store');

        // Chat
        Route::get('chats', [VendorChatController::class, 'index'])->name('chats.index');
        Route::get('chats/{conversation}', [VendorChatController::class, 'show'])->name('chats.show');
        Route::post('chats/{conversation}/messages', [VendorChatController::class, 'store'])->name('chats.messages.store');
    });

// ── User (Penyewa) ─────────────────────────────────────────────────────────────

Route::middleware(['auth', 'verified'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::get('/dashboard', UserDashboard::class)->name('dashboard');

        // Booking
        Route::get('/items/{item}/book', [UserRentalController::class, 'create'])->name('rentals.create');
        Route::post('/items/{item}/book', [UserRentalController::class, 'store'])->name('rentals.store');

        // Checkout & payment (rental belum dibuat sebelum bayar)
        Route::get('/items/{item}/checkout', [UserRentalController::class, 'checkout'])->name('rentals.checkout');
        Route::post('/items/{item}/checkout', [UserRentalController::class, 'confirm'])->name('rentals.confirm');

        // Rentals (sudah paid)
        Route::get('/rentals', [UserRentalController::class, 'index'])->name('rentals.index');
        Route::get('/rentals/{rental}', [UserRentalController::class, 'show'])->name('rentals.show');
        Route::post('/rentals/{rental}/continue-payment', [UserRentalController::class, 'continuePayment'])
            ->name('rentals.continue-payment');

        // Return request (initiated by user)
        Route::post('/rentals/{rental}/return', [ReturnController::class, 'initiate'])->name('rentals.return');

        // Review vendor
        Route::get('/rentals/{rental}/review/create', [UserReviewController::class, 'create'])
            ->name('rentals.review.create');
        Route::post('/rentals/{rental}/review', [UserReviewController::class, 'store'])
            ->name('rentals.review.store');

        // Chat
        Route::get('/chats', [UserChatController::class, 'index'])->name('chats.index');
        Route::post('/chats/start/{vendor}', [UserChatController::class, 'start'])->name('chats.start');
        Route::get('/chats/{conversation}', [UserChatController::class, 'show'])->name('chats.show');
        Route::post('/chats/{conversation}/messages', [UserChatController::class, 'store'])->name('chats.messages.store');
    });

// Breeze default auth routes (login, register, password reset, etc.)
require __DIR__.'/auth.php';
