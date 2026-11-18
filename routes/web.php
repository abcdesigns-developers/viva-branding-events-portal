<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// The portal has no public pages: signed-in users go to the dashboard, everyone else to sign in.
Route::get('/', fn () => redirect()->route(auth()->check() ? 'admin.dashboard.index' : 'login'));

Route::middleware('auth')->group(function () {
    // Section 3 and 4 steps. Who may act is checked inside (RequestFlow), not by a route permission.
    Route::post('/flow/{portal_request}/{action}', [\App\Http\Controllers\Admin\RequestFlowController::class, 'advance'])->name('admin.flow.advance');
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::get('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'open'])->name('notifications.open');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'photo'])->name('profile.photo.store');
    Route::delete('/profile/photo', [ProfileController::class, 'removePhoto'])->name('profile.photo.destroy');
    Route::get('/profile/photo/{user}', [ProfileController::class, 'showPhoto'])->name('profile.photo');
});

require __DIR__.'/auth.php';
require __DIR__.'/backend.php';
