<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboard;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\User\SocialController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| EcoTrack Web Routes
|--------------------------------------------------------------------------
*/

// ---- Root: redirect berdasarkan role atau ke login ----
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route(auth()->user()->role->dashboardRoute());
    }

    return redirect()->route('login');
});

// ---- Auth ----
Route::get('/login', [LoginController::class, 'showForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ---- USER (Karyawan) ----
Route::prefix('user')
    ->name('user.')
    ->middleware(['auth', 'role:user'])
    ->group(function () {
        Route::get('/dashboard', [UserDashboard::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard/chart-data', [UserDashboard::class, 'getChartData'])->name('dashboard.chart-data');
        Route::get('/profile', [UserDashboard::class, 'profile'])->name('profile');
        Route::get('/profile/edit', [UserDashboard::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile/edit', [UserDashboard::class, 'updateProfile'])->name('profile.update');
        Route::get('/friends/add', [SocialController::class, 'search'])->name('friends.add');
        Route::get('/u/{id}', [SocialController::class, 'publicProfile'])->name('public.profile');
        Route::post('/u/{id}/follow', [SocialController::class, 'toggleFollow'])->name('follow.toggle');
        Route::get('/settings', [UserDashboard::class, 'settings'])->name('settings');
        Route::get('/tracking', [UserDashboard::class, 'tracking'])->name('tracking');
        Route::post('/tracking', [UserDashboard::class, 'storeTracking'])->name('tracking.store');
        Route::get('/history', [UserDashboard::class, 'history'])->name('history');
        Route::get('/recommendations', [UserDashboard::class, 'recommendations'])->name('recommendations');
        Route::get('/leaderboard', [UserDashboard::class, 'leaderboard'])->name('leaderboard');
        Route::get('/achievements', [UserDashboard::class, 'achievements'])->name('achievements');
        Route::get('/level-details', [UserDashboard::class, 'levelDetails'])->name('level.details');
        Route::post('/daily-mission/complete', [UserDashboard::class, 'completeDailyMission'])->name('daily-mission.complete');
        Route::post('/daily-mission/claim', [UserDashboard::class, 'claimDailyMission'])->name('daily-mission.claim');
        Route::post('/weekly-mission/{id}/claim', [UserDashboard::class, 'claimWeeklyMission'])->name('weekly-mission.claim');
        Route::post('/complete-task', [UserDashboard::class, 'completeTask'])->name('complete-task');
        
        // Marketplace / Reward
        Route::get('/marketplace', [UserDashboard::class, 'marketplace'])->name('marketplace');
        Route::get('/marketplace/{id}', [UserDashboard::class, 'productDetail'])->name('marketplace.detail');
        Route::post('/marketplace/{id}/buy', [UserDashboard::class, 'buyProduct'])->name('marketplace.buy');
    });

// ---- SELLER ----
Route::prefix('seller')
    ->name('seller.')
    ->middleware(['auth', 'role:seller'])
    ->group(function () {
        Route::get('/dashboard', [SellerDashboard::class, 'dashboard'])->name('dashboard');
        Route::get('/catalog', [SellerDashboard::class, 'catalog'])->name('catalog');
        Route::get('/orders', [SellerDashboard::class, 'orders'])->name('orders');
    });

// ---- ADMIN HR ----
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminDashboard::class, 'users'])->name('users');
        Route::get('/cms', [AdminDashboard::class, 'cms'])->name('cms');
    });
