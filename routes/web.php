<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Jamaah\DashboardController as JamaahDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes - PT. Zein Internasional
|--------------------------------------------------------------------------
| Landing page, autentikasi, dan halaman informasi travel Umrah & Haji Khusus.
*/

// ═══════════════════════════════════════════════════════
// Landing Page (Single Page dengan Anchor Navigation)
// ═══════════════════════════════════════════════════════
Route::get('/', [LandingController::class, 'index'])->name('home');

// Halaman Detail Terpisah
Route::get('/profil', [LandingController::class, 'profil'])->name('profil');
Route::get('/legalitas', [LandingController::class, 'legalitas'])->name('legalitas');
Route::get('/paket', [LandingController::class, 'paket'])->name('paket');
Route::get('/paket/{slug}', [LandingController::class, 'paketDetail'])->name('paket.detail');
Route::get('/galeri', [LandingController::class, 'galeri'])->name('galeri');
Route::get('/kontak', [LandingController::class, 'kontak'])->name('kontak');

// ═══════════════════════════════════════════════════════
// Delivery Gambar Publik On-Demand & Cache WebP
// ═══════════════════════════════════════════════════════
Route::get('/img/{path}', [\App\Http\Controllers\ImageDeliveryController::class, 'deliver'])
    ->where('path', '.*')
    ->middleware('throttle:120,1')
    ->name('images.deliver');


// ═══════════════════════════════════════════════════════
// Auth Jamaah (PRD Section 6.1: Registrasi & Login)
// ═══════════════════════════════════════════════════════
Route::middleware('guest')->group(function () {
    // Registrasi
    Route::get('/daftar', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/daftar', [RegisterController::class, 'register']);

    // Login Jamaah
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Logout (accessible when authenticated)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ═══════════════════════════════════════════════════════
// Area Jamaah (Protected: role jamaah)
// ═══════════════════════════════════════════════════════
Route::middleware(['auth', 'jamaah'])->group(function () {
    // PRD: Halaman "Status Pendaftaran Saya" (/my-registration)
    Route::get('/my-registration', [\App\Http\Controllers\Jamaah\RegistrationController::class, 'show'])->name('jamaah.my-registration');

    Route::prefix('jamaah')->name('jamaah.')->group(function () {
        Route::get('/dashboard', [JamaahDashboardController::class, 'index'])->name('dashboard');
        Route::get('/pendaftaran', [\App\Http\Controllers\Jamaah\RegistrationController::class, 'create'])->name('registration.create');
        Route::post('/pendaftaran', [\App\Http\Controllers\Jamaah\RegistrationController::class, 'store'])->name('registration.store');
        Route::get('/status-pendaftaran', [\App\Http\Controllers\Jamaah\RegistrationController::class, 'show'])->name('registration.status');

        // PRD Section 6.4: Pembayaran DP
        Route::get('/pembayaran-dp', [\App\Http\Controllers\Jamaah\PaymentController::class, 'createDp'])->name('payment.dp');
        Route::post('/pembayaran-dp', [\App\Http\Controllers\Jamaah\PaymentController::class, 'storeDp'])->name('payment.dp.store');

        // PRD Section 6.5: Pelunasan Bertahap / Tabungan Umrah
        Route::get('/pelunasan', [\App\Http\Controllers\Jamaah\PaymentController::class, 'createPelunasan'])->name('payment.pelunasan');
        Route::post('/pelunasan', [\App\Http\Controllers\Jamaah\PaymentController::class, 'storePelunasan'])->name('payment.pelunasan.store');

        // PRD Section 6.10: Pembatalan Pendaftaran & Refund Simulation
        Route::get('/pembatalan', [\App\Http\Controllers\Jamaah\CancellationController::class, 'show'])->name('registration.cancel');
        Route::post('/pembatalan', [\App\Http\Controllers\Jamaah\CancellationController::class, 'cancel'])->name('registration.cancel.process');

        // Penggantian / Re-upload Dokumen Jamaah
        Route::post('/members/{member}/documents', [\App\Http\Controllers\Jamaah\RegistrationController::class, 'updateMemberDocument'])->name('members.documents.update');
        Route::post('/members/{member}/departure-documents/{documentType}', [\App\Http\Controllers\Jamaah\RegistrationController::class, 'uploadDepartureDocument'])->name('members.departure-documents.upload');

        // Profil Jamaah
        Route::get('/profil', [\App\Http\Controllers\Jamaah\ProfileController::class, 'show'])->name('profile');
        Route::put('/profil', [\App\Http\Controllers\Jamaah\ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profil/foto', [\App\Http\Controllers\Jamaah\ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
        Route::delete('/profil/foto', [\App\Http\Controllers\Jamaah\ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
        Route::put('/profil/password', [\App\Http\Controllers\Jamaah\ProfileController::class, 'updatePassword'])->name('profile.password.update');

        // OCR Parser Route
        Route::post('/ocr/parse', [\App\Http\Controllers\Jamaah\OcrController::class, 'parse'])->name('ocr.parse');
    });
});

// ═══════════════════════════════════════════════════════
// Auth Admin (Login terpisah di /admin/login)
// ═══════════════════════════════════════════════════════
Route::prefix('admin')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showForm'])->name('admin.login');
        Route::post('/login', [AdminLoginController::class, 'login']);
    });

    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout')->middleware('auth');

    // Admin area (Protected: role admin)
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::resource('packages', \App\Http\Controllers\Admin\PackageController::class)->names('admin.packages');
        
        Route::post('packages/{package}/upload-photo', [\App\Http\Controllers\Admin\PackageController::class, 'uploadPhoto'])->name('admin.packages.upload-photo');
        
        // Sub-Paket / Varian
        Route::get('packages/{package}/variants/create', [\App\Http\Controllers\Admin\PackageVariantController::class, 'create'])->name('admin.packages.variants.create');
        Route::post('packages/{package}/variants', [\App\Http\Controllers\Admin\PackageVariantController::class, 'store'])->name('admin.packages.variants.store');
        Route::get('packages/{package}/variants/{variant}/edit', [\App\Http\Controllers\Admin\PackageVariantController::class, 'edit'])->name('admin.packages.variants.edit');
        Route::put('packages/{package}/variants/{variant}', [\App\Http\Controllers\Admin\PackageVariantController::class, 'update'])->name('admin.packages.variants.update');
        Route::delete('packages/{package}/variants/{variant}', [\App\Http\Controllers\Admin\PackageVariantController::class, 'destroy'])->name('admin.packages.variants.destroy');

        // ── Master Data: Hotel ──
        Route::resource('hotels', \App\Http\Controllers\Admin\HotelController::class)->names('admin.hotels');
        Route::get('api/hotels/search', [\App\Http\Controllers\Admin\HotelController::class, 'search'])->name('admin.api.hotels.search');

        // ── Master Data: Maskapai ──
        Route::resource('airlines', \App\Http\Controllers\Admin\AirlineController::class)->names('admin.airlines');
        Route::get('api/airlines/search', [\App\Http\Controllers\Admin\AirlineController::class, 'search'])->name('admin.api.airlines.search');

        // Manajemen User / Jamaah
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('admin.users.show');
        Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::get('/members/{member}/documents/{type}/preview', [\App\Http\Controllers\Admin\UserController::class, 'viewDocument'])->name('admin.members.documents.preview');

        // PRD Section 6.6: Verifikasi Dokumen & Pendaftaran
        Route::get('/registrations', [\App\Http\Controllers\Admin\RegistrationController::class, 'index'])->name('admin.registrations.index');
        Route::get('/registrations/{registration}', [\App\Http\Controllers\Admin\RegistrationController::class, 'show'])->name('admin.registrations.show');
        Route::post('/members/{member}/verify', [\App\Http\Controllers\Admin\RegistrationController::class, 'verifyMember'])->name('admin.members.verify');
        Route::post('/members/{member}/documents', [\App\Http\Controllers\Admin\RegistrationController::class, 'updateMemberDocument'])->name('admin.members.documents.update');
        Route::delete('/members/{member}/documents/{documentType}', [\App\Http\Controllers\Admin\RegistrationController::class, 'deleteMemberDocument'])->name('admin.members.documents.delete');
        Route::post('/jamaah-documents/{document}/verify', [\App\Http\Controllers\Admin\RegistrationController::class, 'verifyDepartureDocument'])->name('admin.documents.verify');
        Route::post('/registrations/{registration}/complete', [\App\Http\Controllers\Admin\RegistrationController::class, 'markCompleted'])->name('admin.registrations.complete');
        Route::post('/departures/{package}/complete', [\App\Http\Controllers\Admin\RegistrationController::class, 'completeDeparture'])->name('admin.departures.complete');
        Route::post('/packages/{package}/complete-participants', [\App\Http\Controllers\Admin\RegistrationController::class, 'completeDeparture'])->name('admin.packages.complete-participants');
        Route::get('/departures/{package}', function(\App\Models\Package $package) {
            return redirect()->route('admin.packages.show', $package);
        })->name('admin.departures.show');

        // PRD Section 6.7: Verifikasi Pembayaran
        Route::get('/payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('admin.payments.index');
        Route::post('/payments/{payment}/verify', [\App\Http\Controllers\Admin\PaymentController::class, 'verify'])->name('admin.payments.verify');

        // PRD Section 6.10: Validasi Pembatalan Jamaah
        Route::get('/cancellations', [\App\Http\Controllers\Admin\CancellationController::class, 'index'])->name('admin.cancellations.index');
        Route::post('/cancellations/{cancellation}/verify', [\App\Http\Controllers\Admin\CancellationController::class, 'verify'])->name('admin.cancellations.verify');

        // ── Galeri Media (Foto & Video) ──
        Route::resource('galleries', \App\Http\Controllers\Admin\GalleryController::class)->names('admin.galleries');
    });
});

// ═══════════════════════════════════════════════════════
// Dokumen Resmi (Invoice & Kwitansi) — Auth Protected
// ═══════════════════════════════════════════════════════
Route::middleware('auth')->group(function () {
    Route::get('/documents/invoice/{registration}', [\App\Http\Controllers\DocumentController::class, 'invoice'])->name('documents.invoice');
    Route::get('/documents/invoice/{registration}/download', [\App\Http\Controllers\DocumentController::class, 'downloadInvoice'])->name('documents.invoice.download');
    Route::get('/documents/invoice/{registration}/pdf', [\App\Http\Controllers\DocumentController::class, 'downloadInvoicePdf'])->name('documents.invoice.pdf');
    Route::get('/documents/receipt/{payment}', [\App\Http\Controllers\DocumentController::class, 'receipt'])->name('documents.receipt');
    Route::get('/documents/receipt/{payment}/download', [\App\Http\Controllers\DocumentController::class, 'downloadReceipt'])->name('documents.receipt.download');
    Route::get('/documents/receipt/{payment}/pdf', [\App\Http\Controllers\DocumentController::class, 'downloadReceiptPdf'])->name('documents.receipt.pdf');
});

