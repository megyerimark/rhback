<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShopController;
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| NYILVÁNOS ÚTVONALAK (Bárki elérheti token nélkül)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');
Route::get('/events', [EventController::class, 'index']);


/*
|--------------------------------------------------------------------------
| VÉDETT ÚTVONALAK (Csak érvényes bejelentkezési tokennel)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->group(function () {

    // Bejelentkezett felhasználó alapadatai
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // --- 🚨 ÚJ: ADMIN DAHSBOARD VÉGPONT ---
    // Lekéri az összes felhasználót a Spatie rangjukkal (roles) együtt az Angular táblázathoz
    Route::get('/users', function () {
        return User::with('roles')->get();
    });

    // Bulik kezelése
    Route::post('/events', [EventController::class, 'store']);

    // Vélemények (Reviews)
    Route::get('/events/{eventId}/reviews', [ReviewController::class, 'index']);
    Route::post('/events/{eventId}/reviews', [ReviewController::class, 'store']);

    // Shop rendszer
    Route::get('/shop', [ShopController::class, 'index']);
    Route::post('/shop/buy/{itemId}', [ShopController::class, 'buy']);

    // Kijelentkezés
    Route::post('/logout', [AuthController::class, 'logout']);

    // Bejelentkezett felhasználó profil adatai + megvett itemek + rangok
    Route::get('/me', function (Request $request) {
        return response()->json([
            'user' => $request->user()->load('virtualItems'),
            'roles' => $request->user()->getRoleNames()
        ], 200);
    });
    Route::post('/events/sync-facebook', [EventController::class, 'syncFacebookEvents']);

});