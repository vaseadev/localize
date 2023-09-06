<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request) {
        if (! Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json(__('messages.failed_login'), 401);
        }

        $token = Auth::user()->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => __('messages.success_login'),
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => Auth::user()
        ], 202);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => __('messages.success_logout')
        ]);
    }
}
