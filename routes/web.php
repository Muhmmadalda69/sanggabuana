<?php

use App\Http\Controllers\HomeController;
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
Route::post('/kontak', [HomeController::class, 'contact'])->name('contact.store');
Route::get('/halaman/{slug}', [HomeController::class, 'page'])->name('page.show');
Route::post('/ulasan', [HomeController::class, 'storeTestimonial'])->name('testimonials.store');

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
    Route::get('/monitoring', [DashboardController::class, 'monitoringIndex'])->name('monitoring.index');
    Route::post('/monitoring/{visitor}/checkout', [DashboardController::class, 'monitoringCheckout'])->name('monitoring.checkout');

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
});
