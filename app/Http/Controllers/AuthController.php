<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
       // Regisztráció

       $validateData = $request->validate([

        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => ['required','confirmed', Password::defaults()],
       ]);  
       $user = User::create([
        'name'=>$validateData['name'],
        'email'=>$validateData['email'],
        'password'=>Hash::make($validateData['password']),
       ]);

       $user->assignRole('user');

       event(new Registered($user));

       return response()->json([
        'message' => 'Sikeres regisztráció! Kérlek, ellenőrizd az e-mail fiókodat az aktiváláshoz.',
        'user' => $user,
       ], 201);
    }
    //email validáció
       public function verify(Request $request, $id, $hash)
       {
        $user = User::findOrFail($id);
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Érvénytelen vagy lejárt link.'], 400);
        }
        if ( $user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Ez a fiók már aktiválva van.'], 400);
        }

        $user->markEmailAsVerified();
        return response()->json(['message' => 'Sikeres aktiválás! Most már bejelentkezhetsz.'], 200);
        }

        //Bejelentkezés
        public function login(Request $request){
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
            $user = User::where('email', $request->email)->first();


            if(!user || ! Hash::check($request->password, $user->password))
            {
                return response()->json(['message' => 'Hibás email vagy jelszó.'], 401);
            }

            if (!$user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Kérlek, először aktiváld a fiókodat az emailben kapott linkre kattintva.'], 403);
            }


            $token = $user->createToken('ravehiuse_auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Sikeres bejelentkezés a RaveHouse-ba!',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user, 
                'role' => $user->getRoleNames(), 
            ], 200);
        }

}
