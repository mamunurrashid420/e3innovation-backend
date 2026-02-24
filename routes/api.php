<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Test route: GET /api/ping → 200 (verify API is reachable)
Route::get('/ping', function () {
    return response()->json(['ok' => true, 'message' => 'API is reachable']);
});
use App\Http\Controllers\SliderController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\UploadController;

Route::group(['prefix' => 'auth', 'middleware' => 'api'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    Route::get('user', [AuthController::class, 'me']);
});

// Public Routes (No Authentication Required)
Route::prefix('public')->group(function () {
    Route::get('/sliders', [SliderController::class, 'indexPublic']);
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/team-members', [TeamMemberController::class, 'index']);
    Route::get('/settings/{group}', [SettingsController::class, 'getByGroupPublic']);

    // Direct settings routes for frontend compatibility
    Route::get('/settings/appearance', function() {
        return app(SettingsController::class)->getByGroupPublic('appearance');
    });
    Route::get('/settings/footer', function() {
        return app(SettingsController::class)->getByGroupPublic('footer');
    });
    Route::get('/settings/social', function() {
        return app(SettingsController::class)->getByGroupPublic('social');
    });

    Route::get('/stats', [SettingsController::class, 'getStats']);
});

// Public API Routes (Without /public prefix for frontend compatibility)
Route::get('sliders', [SliderController::class, 'indexPublic']);
Route::get('appearance', function() {
    return app(SettingsController::class)->getByGroupPublic('appearance');
});
Route::get('footer', function() {
    return app(SettingsController::class)->getByGroupPublic('footer');
});
Route::get('social', function() {
    return app(SettingsController::class)->getByGroupPublic('social');
});

// Admin Routes (Authentication Required)
Route::group(['middleware' => 'auth:api', 'prefix' => 'admin'], function () {
    Route::apiResource('sliders', SliderController::class);
    Route::put('sliders/{slider}/toggle-status', [SliderController::class, 'toggleStatus']);
    Route::put('sliders/reorder', [SliderController::class, 'reorder']);
    Route::get('dashboard/stats', [ContactMessageController::class, 'stats']);

    // Contact Messages
    Route::get('/contact/messages', [ContactMessageController::class, 'index']);
    Route::put('/contact/messages/{id}/mark-read', [ContactMessageController::class, 'markAsRead']);
    Route::delete('/contact/messages/{id}', [ContactMessageController::class, 'destroy']);

    // Settings
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::get('/settings/{group}', [SettingsController::class, 'getByGroup']);
    Route::post('/settings', [SettingsController::class, 'update']);
});

// Public API Routes (Alternative without /public prefix for backward compatibility)
Route::get('services', [ServiceController::class, 'index']);
Route::get('services/{slug}', [ServiceController::class, 'showSlug']);
Route::post('services', [ServiceController::class, 'store'])->middleware('auth:api');
Route::put('services/{service}', [ServiceController::class, 'update'])->middleware('auth:api');
Route::delete('services/{service}', [ServiceController::class, 'destroy'])->middleware('auth:api');

Route::get('projects', [ProjectController::class, 'index']);
Route::get('projects/{slug}', [ProjectController::class, 'showSlug']);
Route::post('projects', [ProjectController::class, 'store'])->middleware('auth:api');
Route::put('projects/{project}', [ProjectController::class, 'update'])->middleware('auth:api');
Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->middleware('auth:api');

Route::get('team-members', [TeamMemberController::class, 'index']);
Route::get('team-members/{team}', [TeamMemberController::class, 'show']);
Route::post('team-members', [TeamMemberController::class, 'store'])->middleware('auth:api');
Route::put('team-members/{team}', [TeamMemberController::class, 'update'])->middleware('auth:api');
Route::delete('team-members/{team}', [TeamMemberController::class, 'destroy'])->middleware('auth:api');

Route::post('contact', [ContactMessageController::class, 'store']);
Route::get('contact/messages', [ContactMessageController::class, 'index'])->middleware('auth:api');
Route::delete('contact/messages/{id}', [ContactMessageController::class, 'destroy'])->middleware('auth:api');
Route::put('contact/messages/{id}/mark-read', [ContactMessageController::class, 'markRead'])->middleware('auth:api');

Route::post('upload', [UploadController::class, 'store'])->middleware('auth:api');
Route::post('media/upload', [UploadController::class, 'store'])->middleware('auth:api');
Route::delete('media/{id}', function () {
    return response()->json(['message' => 'Media delete not implemented'], 501);
})->middleware('auth:api');
