<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) { 
            $user = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'success' => true, 
                'token' => $token,
                'user' => $user
            ]); 
            

        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials.'
        ], 401);
    }
    
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.'
        ]);
    }
}
