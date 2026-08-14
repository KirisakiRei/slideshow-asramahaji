<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\GroupItemController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PhotoGroupController;
use App\Http\Controllers\RunningTextController;
use App\Http\Controllers\SignageController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// Public authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Public display routes (no auth required)
Route::get('/display', [DisplayController::class, 'index'])->name('display.all');
Route::get('/display/status', [DisplayController::class, 'status']);
Route::get('/display/{group}', [DisplayController::class, 'show'])->name('display.show');

// Authenticated admin routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Settings
    Route::get('/settings/password', [SettingsController::class, 'showPassword'])->name('settings.password');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Digital signage content management
    Route::get('/signage/header', [SignageController::class, 'header'])->name('signage.header');
    Route::post('/signage/header', [SignageController::class, 'updateHeader'])->name('signage.header.update');
    Route::delete('/signage/header/logo', [SignageController::class, 'removeLogo'])->name('signage.header.logo.remove');

    Route::get('/signage/main', [SignageController::class, 'main'])->name('signage.main');
    Route::post('/signage/main', [SignageController::class, 'updateMain'])->name('signage.main.update');

    Route::get('/signage/facilities', [SignageController::class, 'facilities'])->name('signage.facilities');
    Route::post('/signage/facilities', [SignageController::class, 'updateFacilities'])->name('signage.facilities.update');

    Route::get('/signage/next-event', [SignageController::class, 'nextEvent'])->name('signage.next-event');
    Route::post('/signage/next-event', [SignageController::class, 'updateNextEvent'])->name('signage.next-event.update');

    Route::get('/signage/footer', [SignageController::class, 'footer'])->name('signage.footer');
    Route::post('/signage/footer', [SignageController::class, 'updateFooter'])->name('signage.footer.update');

    // Running text management
    Route::get('/running-texts', [RunningTextController::class, 'index'])->name('running-texts.index');
    Route::post('/running-texts', [RunningTextController::class, 'store'])->name('running-texts.store');
    Route::put('/running-texts/{runningText}', [RunningTextController::class, 'update'])->name('running-texts.update');
    Route::delete('/running-texts/{runningText}', [RunningTextController::class, 'destroy'])->name('running-texts.destroy');
    Route::post('/running-texts/{runningText}/toggle', [RunningTextController::class, 'toggleActive'])->name('running-texts.toggle');
    Route::post('/running-texts/{runningText}/move-up', [RunningTextController::class, 'moveUp'])->name('running-texts.move-up');
    Route::post('/running-texts/{runningText}/move-down', [RunningTextController::class, 'moveDown'])->name('running-texts.move-down');

    // Photo CRUD
    Route::resource('photos', PhotoController::class)->except(['show', 'create']);

    // Video CRUD
    Route::resource('videos', VideoController::class)->except(['show', 'create'])->parameters([
        'videos' => 'video',
    ]);

    // Photo Group CRUD
    Route::resource('photo-groups', PhotoGroupController::class)->except(['show'])->parameters([
        'photo-groups' => 'group',
    ]);
    Route::post('/photo-groups/{group}/toggle', [PhotoGroupController::class, 'toggleActive'])->name('photo-groups.toggle');
    Route::post('/photo-groups/{group}/move-up', [PhotoGroupController::class, 'moveUp'])->name('photo-groups.move-up');
    Route::post('/photo-groups/{group}/move-down', [PhotoGroupController::class, 'moveDown'])->name('photo-groups.move-down');

    // Group Items Management
    Route::get('/photo-groups/{group}/items', [GroupItemController::class, 'index'])->name('group-items.index');
    Route::post('/photo-groups/{group}/items', [GroupItemController::class, 'store'])->name('group-items.store');
    Route::put('/photo-groups/{group}/items/{item}', [GroupItemController::class, 'update'])->name('group-items.update');
    Route::post('/photo-groups/{group}/items/{item}/move-up', [GroupItemController::class, 'moveUp'])->name('group-items.move-up');
    Route::post('/photo-groups/{group}/items/{item}/move-down', [GroupItemController::class, 'moveDown'])->name('group-items.move-down');
    Route::delete('/photo-groups/{group}/items/{item}', [GroupItemController::class, 'destroy'])->name('group-items.destroy');
});
