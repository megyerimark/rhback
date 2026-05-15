<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified; // Ez hiányzott a tetejéről!
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // 1. REGISZTRÁCIÓ
    public function register(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);  

        $user = User::create([
            'name' => $validateData['name'],
            'email' => $validateData['email'],
            'password' => Hash::make($validateData['password']),
        ]);

        $user->assignRole('user');

        event(new Registered($user));

        return response()->json([
            'message' => 'Sikeres regisztráció! Kérlek, ellenőrizd az e-mail fiókodat az aktiváláshoz.',
            'user' => $user,
        ], 201);
    }

    // 2. EMAIL MEGERŐSÍTÉS (Aktiválás az Angular felől)
    public function verify(Request $request, $id, $hash)
    {
        if (! $request->hasValidSignature()) {
            return response()->json(['message' => 'Érvénytelen vagy lejárt aláírás.'], 401);
        }

        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Érvénytelen biztonsági kód.'], 401);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'A fiók már korábban aktiválva lett.'], 200);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(['message' => 'Sikeres email megerősítés!'], 200);
    }

    // 3. BEJELENTKEZÉS (Különválasztva, aktiválás ellenőrzéssel!)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Hibás email vagy jelszó.'], 401);
        }

        // Ha még nincs aktiválva, nem engedjük be!
        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Kérlek, először aktiváld a fiókodat az emailben kapott linkre kattintva.'], 403);
        }

        // Ha minden jó, generáljuk a tokent
        $token = $user->createToken('ravehouse_auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Sikeres bejelentkezés a RaveHouse-ba!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user, 
            'roles' => $user->getRoleNames(), 
        ], 200);
    }

    // 4. KIJELENTKEZÉS
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sikeres kijelentkezés!'], 200);
    }
}