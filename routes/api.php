<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



route::post('/register', [AuthController::class, 'register']);
route::post('/login', [AuthController::class, 'login']);
route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');
route::get("/events", [EventController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {

Route::get('/user', function (Request $request) {
    return $request->user();});


route::post("/events", [EventController::class, 'store']);

Route::get('/events/{eventId}/reviews', [ReviewController::class, 'index']);
Route::post('/events/{eventId}/reviews', [ReviewController::class, 'store']);

Route::get('/shop', [ShopController::class, 'index']);
Route::post('/shop/buy/{itemId}', [ShopController::class, 'buy']);

Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/me', function (Request $request) {
        return response()->json([
            'user' => $request->user()->load('virtualItems'),
            'roles' => $request->user()->getRoleNames()
        ], 200);
    });


});
