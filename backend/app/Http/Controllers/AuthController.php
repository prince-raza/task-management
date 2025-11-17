<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \App\Http\Requests\LoginRequest  $request
     *
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function login(LoginRequest $request)
    {
        $user = User::whereEmail($request->email)->first();
        $passwordMatch = Hash::check($request->password, $user->password);

        if (!$user || !$passwordMatch) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        };

        $token = $user->createToken($user->name);

        return  [
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }

    /**
     * Remove the tokens from the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
