<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\ExpenseDeductionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin'
            ? redirect('/admin/dashboard')
            : redirect('/tenant/vehicle');
    }
    return redirect('/login');
});

// AUTH
Route::get('/login',     [UserController::class, 'showLogin'])->name('login');
Route::post('/login',    [UserController::class, 'login']);
Route::get('/register',  [UserController::class, 'showRegister'])->name('register');
Route::post('/register', [UserController::class, 'register']);
Route::post('/logout',   [UserController::class, 'logout'])->name('logout');
Route::get('/logout',    [UserController::class, 'logout']);

Route::middleware(['auth'])->group(function () {

    Route::post('/profile/update',          [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/preferences',     [ProfileController::class, 'updatePreferences'])->name('profile.preferences');
    Route::post('/profile/verify-password', [ProfileController::class, 'verifyPassword'])->name('profile.verifyPassword');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.changePassword');

    Route::post('/tenant/feedback', [TenantController::class, 'submitFeedback'])->name('tenant.feedback.store');

    Route::get('/admin/feedback',              [AdminController::class, 'feedback'])->name('admin.feedback');
    Route::post('/admin/feedback/{id}/action', [AdminController::class, 'feedbackAction'])->name('admin.feedback.action');

    // Notifications
    Route::get('/notifications',           [NotificationController::class, 'index']);
    Route::get('/notifications/count',     [NotificationController::class, 'count']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);
    Route::post('/notifications/{id}/read',[NotificationController::class, 'markRead']);
    Route::delete('/notifications',        [NotificationController::class, 'destroyBulk']);
    Route::delete('/notifications/{id}',   [NotificationController::class, 'destroy']);

    // ADMIN
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Vehicles
    Route::get('/admin/vehicle',              [VehicleController::class, 'index'])->name('admin.vehicle');
    Route::post('/admin/vehicle',             [VehicleController::class, 'store'])->name('admin.vehicle.store');
    Route::put('/admin/vehicle/{vehicle}',    [VehicleController::class, 'update'])->name('admin.vehicle.update');
    Route::delete('/admin/vehicle/{vehicle}', [VehicleController::class, 'destroy'])->name('admin.vehicle.destroy');

    // Bookings (now includes expense management)
    Route::get('/admin/bookings',              [AdminController::class, 'bookings'])->name('admin.bookings');
    Route::post('/admin/bookings/{id}/action', [AdminController::class, 'bookingAction'])->name('admin.bookings.action');
    Route::get('/admin/bookings/{id}',         [AdminController::class, 'bookingDetail'])->name('admin.bookings.detail');

    // Transactions / Expenses endpoints (used by bookings page)
    Route::get('/admin/transactions/{id}/expenses',  [AdminController::class, 'getExpenses'])->name('admin.transactions.expenses');
    Route::post('/admin/transactions/{id}/expenses', [AdminController::class, 'saveExpenses'])->name('admin.transactions.expenses.save');

    // Deductions
    Route::post('/admin/deductions',        [ExpenseDeductionController::class, 'store'])->name('admin.deductions.store');
    Route::delete('/admin/deductions/{id}', [ExpenseDeductionController::class, 'destroy'])->name('admin.deductions.destroy');

    // Expenses page removed — redirect to bookings
    Route::get('/admin/expenses', function () {
        return redirect()->route('admin.bookings');
    })->name('admin.expenses');

    // Tenants
    Route::get('/admin/tenants', [AdminController::class, 'tenants'])->name('admin.tenants');

    // TENANT
    Route::get('/tenant/vehicle',                    [TenantController::class, 'vehicle'])->name('tenant.vehicle');
    Route::get('/tenant/history',                    [TenantController::class, 'history'])->name('tenant.history');
    Route::get('/tenant/reservation',                [TenantController::class, 'reservation'])->name('tenant.reservation');
    Route::post('/tenant/reservation/store',         [TenantController::class, 'storeReservation'])->name('tenant.reservation.store');
    Route::delete('/tenant/reservation/{id}/delete', [TenantController::class, 'deleteReservation'])->name('tenant.reservation.delete');
    Route::delete('/tenant/history/{id}/delete',     [TenantController::class, 'deleteHistory'])->name('tenant.history.delete');
    Route::delete('/tenant/history/delete-selected', [TenantController::class, 'deleteSelectedHistory'])->name('tenant.history.deleteSelected');

// ─────────────────────────────────────────────────────────────────────────────
// IDAGDAG SA web.php — kasama ng ibang tenant routes (around line 72+)
// ─────────────────────────────────────────────────────────────────────────────

Route::post('/tenant/reservation/{id}/cancel', [TenantController::class, 'cancelReservation'])->name('tenant.reservation.cancel');
});