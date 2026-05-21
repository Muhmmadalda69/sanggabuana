<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\VisitorAuthController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\AdminAuth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/destinasi', [HomeController::class, 'destinations'])->name('destinations');
Route::get('/destinasi/{slug}', [HomeController::class, 'destination'])->name('destination.detail');
Route::get('/destinasi/{slug}/registrasi', [HomeController::class, 'registerDatePicker'])->name('destination.register.date');
Route::get('/destinasi/{slug}/registrasi/{date}', [HomeController::class, 'registerForm'])->name('destination.register');
Route::post('/destinasi/{slug}/registrasi/{date}', [HomeController::class, 'registerStore'])->name('destination.register.store');
Route::get('/destinasi/{slug}/kuota', [HomeController::class, 'quotaApi'])->name('destination.quota');
Route::get('/destinasi/{slug}/kuota-bulan', [HomeController::class, 'quotaMonth'])->name('destination.quota.month');
Route::post('/kontak', [HomeController::class, 'contact'])->name('contact.store');
Route::get('/halaman/{slug}', [HomeController::class, 'page'])->name('page.show');
Route::post('/ulasan', [HomeController::class, 'storeTestimonial'])->name('testimonials.store');

/*
|--------------------------------------------------------------------------
| Visitor Auth Routes
|--------------------------------------------------------------------------
*/
Route::prefix('akun')->name('visitor.')->group(function () {
    Route::get('/login', [VisitorAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [VisitorAuthController::class, 'login'])->name('login.post');
    Route::get('/register', [VisitorAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [VisitorAuthController::class, 'register'])->name('register.post');
    Route::post('/logout', [VisitorAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:visitor')->group(function () {
        Route::get('/', [VisitorAuthController::class, 'dashboard'])->name('dashboard');
        Route::get('/riwayat', [VisitorAuthController::class, 'riwayat'])->name('riwayat');
        Route::get('/tiket-saya', [VisitorAuthController::class, 'tiketSaya'])->name('tiket-saya');
        Route::get('/tiket/{groupId}', [VisitorAuthController::class, 'viewTiket'])->name('tiket-detail');
    });
});

/*
|--------------------------------------------------------------------------
| Payment Routes (Midtrans)
|--------------------------------------------------------------------------
*/
Route::get('/payment/{paymentToken}/pay', [PaymentController::class, 'pay'])->name('payment.pay');
Route::get('/payment/{paymentToken}/finish', [PaymentController::class, 'finish'])->name('payment.finish');
Route::get('/payment/{paymentToken}/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/{paymentToken}/status', [PaymentController::class, 'status'])->name('payment.status');
Route::post('/payment/{paymentToken}/change-method', [PaymentController::class, 'changeMethod'])->name('payment.change-method');
Route::post('/payment/{paymentToken}/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
Route::post('/payment/notification', [PaymentController::class, 'notificationHandler'])->name('payment.notification');

/*
|--------------------------------------------------------------------------
| Admin Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Admin Protected Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(AdminAuth::class)->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/notifications/unread-count', [DashboardController::class, 'unreadCount'])->name('notifications.unread');
    Route::get('/dashboard/realtime-stats', [DashboardController::class, 'realtimeStats'])->name('dashboard.realtime-stats');

    // POS & Monitoring routes for Cashiers
    Route::get('/pos', [DashboardController::class, 'posIndex'])->name('pos.index');
    Route::post('/pos', [DashboardController::class, 'posStore'])->name('pos.store');
    Route::get('/pos/quota', [DashboardController::class, 'posQuota'])->name('pos.quota');
    Route::get('/monitoring', [DashboardController::class, 'monitoringIndex'])->name('monitoring.index');
    Route::post('/monitoring/{visitor}/checkout', [DashboardController::class, 'monitoringCheckout'])->name('monitoring.checkout');
    Route::post('/monitoring/group/{groupId}/checkout', [DashboardController::class, 'monitoringGroupCheckout'])->name('monitoring.group-checkout');
    Route::post('/monitoring/partial-checkout', [DashboardController::class, 'monitoringPartialCheckout'])->name('monitoring.partial-checkout');
    Route::patch('/monitoring/{visitor}/status', [DashboardController::class, 'monitoringUpdateStatus'])->name('monitoring.update-status');

    Route::resource('destinations', DestinationController::class)->except(['show']);

    // Admin & Superadmin only routes
    Route::middleware('admin_or_superadmin')->group(function () {
        Route::resource('galleries', GalleryController::class)->except(['show']);
        Route::resource('testimonials', TestimonialController::class)->except(['show']);
        Route::patch('testimonials/{testimonial}/toggle', [TestimonialController::class, 'toggle'])->name('testimonials.toggle');
        Route::resource('pages', PageController::class)->except(['show']);

        Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
        Route::get('/contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
        Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // RBAC routes - only accessible by superadmin role
    Route::middleware('superadmin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    // Visitor Accounts Management - admin & superadmin
    Route::middleware('admin_or_superadmin')->group(function () {
        Route::get('/visitor-accounts', [DashboardController::class, 'visitorAccountsIndex'])->name('visitor-accounts.index');
        Route::post('/visitor-accounts/{visitorAccount}/activate', [DashboardController::class, 'visitorAccountActivate'])->name('visitor-accounts.activate');
        Route::post('/visitor-accounts/{visitorAccount}/ban', [DashboardController::class, 'visitorAccountBan'])->name('visitor-accounts.ban');
    });
});
