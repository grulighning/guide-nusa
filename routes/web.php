<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DestinasiController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\PemanduController;
use App\Http\Controllers\PemanduDestinasiController;

// ── Health check ───────────────────────────────────────────────
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// ── Public routes ──────────────────────────────────────────────
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/destinasi', [DestinasiController::class, 'index'])->name('destinasi.index');
Route::get('/destinasi/{destinasi}', [DestinasiController::class, 'show'])->name('destinasi.show');
Route::get('/paket', [PaketController::class, 'index'])->name('paket.index');
Route::get('/pemandu', [PemanduController::class, 'index'])->name('pemandu.index');

// ── Authenticated routes ────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Booking dari halaman destinasi
    Route::get('/bookings/create/{destination}/{guide}', [App\Http\Controllers\BookingController::class, 'create'])->name('bookings.create');

    // Review
    Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::get('/ulasan-saya', [DashboardController::class, 'myReviews'])->name('dashboard.reviews');

    // Wisatawan bisa booking
    Route::post('/paket/{paket}/booking', [PaketController::class, 'booking'])->name('paket.booking');
    Route::post('/pemandu/{pemandu}/booking', [PemanduController::class, 'booking'])->name('pemandu.booking');

    // User konfirmasi tour selesai
    Route::patch('/bookings/{booking}/confirm-complete', [PemanduController::class, 'userConfirmComplete'])->name('user.bookings.confirm-complete');

    // Hanya pemandu yang bisa buat paket & kelola destinasi
    Route::middleware(['role:pemandu'])->group(function () {
        // ── Dashboard & Profile Pemandu ──
        Route::get('/pemandu/dashboard', [PemanduController::class, 'dashboard'])->name('pemandu.dashboard');
        Route::get('/pemandu/profile', [PemanduController::class, 'profile'])->name('pemandu.profile');
        
        // ── Paket ──
        Route::get('/paket/create', [PaketController::class, 'create'])->name('paket.create');
        Route::post('/paket', [PaketController::class, 'store'])->name('paket.store');
        Route::get('/paket/{paket}/edit', [PaketController::class, 'edit'])->name('paket.edit');
        Route::put('/paket/{paket}', [PaketController::class, 'update'])->name('paket.update');
        Route::delete('/paket/{paket}', [PaketController::class, 'destroy'])->name('paket.destroy');
        Route::patch('/pemandu/bookings/{booking}/confirm', [PemanduController::class, 'confirmBooking'])->name('pemandu.bookings.confirm');
        Route::patch('/pemandu/bookings/{booking}/reject', [PemanduController::class, 'rejectBooking'])->name('pemandu.bookings.reject');
        Route::patch('/pemandu/bookings/{booking}/complete', [PemanduController::class, 'completeBooking'])->name('pemandu.bookings.complete');

        // ── Profil Pemandu ──
        Route::put('/pemandu/profile', [PemanduController::class, 'updateProfile'])->name('pemandu.profile.update');

        // ── Ketersediaan Pemandu ──
        Route::put('/pemandu/ketersediaan', [PemanduController::class, 'updateKetersediaan'])->name('pemandu.ketersediaan.update');

        // ── Destinasi milik pemandu ──
        Route::get('/pemandu/destinasi', [PemanduDestinasiController::class, 'index'])->name('pemandu.destinasi.index');
        Route::get('/pemandu/destinasi/create', [PemanduDestinasiController::class, 'create'])->name('pemandu.destinasi.create');
        Route::post('/pemandu/destinasi', [PemanduDestinasiController::class, 'store'])->name('pemandu.destinasi.store');
        Route::get('/pemandu/destinasi/{destinasi}/edit', [PemanduDestinasiController::class, 'edit'])->name('pemandu.destinasi.edit');
        Route::put('/pemandu/destinasi/{destinasi}', [PemanduDestinasiController::class, 'update'])->name('pemandu.destinasi.update');
        Route::delete('/pemandu/destinasi/{destinasi}', [PemanduDestinasiController::class, 'destroy'])->name('pemandu.destinasi.destroy');
    });
});

// ── Chat ──
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{user}', [App\Http\Controllers\ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{user}', [App\Http\Controllers\ChatController::class, 'store'])->name('chat.store');
});

// ── Breeze auth routes (login, register, forgot password, dll) ──
Route::get('/pemandu/{pemandu}', [PemanduController::class, 'show'])->name('pemandu.show');

require __DIR__ . '/auth.php';