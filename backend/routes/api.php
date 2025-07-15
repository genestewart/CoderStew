<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\BookingController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes
Route::prefix('v1')->group(function () {
    // Projects
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);
    Route::get('/projects/category/{category}', [ProjectController::class, 'byCategory']);
    Route::get('/projects/technology/{technology}', [ProjectController::class, 'byTechnology']);

    // Testimonials
    Route::get('/testimonials', [TestimonialController::class, 'index']);
    Route::get('/testimonials/featured', [TestimonialController::class, 'featured']);

    // Contact
    Route::post('/contact', [ContactController::class, 'store']);

    // Newsletter
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);
    Route::post('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe']);

    // Microsoft Bookings
    Route::get('/bookings/availability', [BookingController::class, 'availability']);
    Route::post('/bookings/schedule', [BookingController::class, 'schedule']);

    // Performance metrics
    Route::get('/metrics/performance', function () {
        return response()->json([
            'timestamp' => now()->toISOString(),
            'server_time' => microtime(true) - LARAVEL_START,
        ]);
    });
});

// Protected API routes (require authentication)
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Admin routes would go here
    Route::get('/admin/dashboard', function () {
        return response()->json(['message' => 'Admin dashboard']);
    });
});
