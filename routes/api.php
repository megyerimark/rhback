<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



route::post('/register', [AuthController::class, 'register']);
route::post('/login', [AuthController::class, 'login']);
route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');

Route::middleware(['auth:sanctum'])->group(function () {

Route::get('/user', function (Request $request) {
    return $request->user();});

route::get("/events", [EventController::class, 'index']);
route::post("/events", [EventController::class, 'store']);

Route::get('/events/{eventId}/reviews', [ReviewController::class, 'index']);
Route::post('/events/{eventId}/reviews', [ReviewController::class, 'store']);


});
