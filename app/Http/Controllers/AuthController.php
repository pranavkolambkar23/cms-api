<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // POST /api/login
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    // POST /api/logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
