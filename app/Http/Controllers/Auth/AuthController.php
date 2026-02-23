<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Exception;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'department' => 'required|string|max:100',
                'year' => 'required|integer|min:1|max:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'error_code' => 'VALIDATION_ERROR'
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'department' => $request->department,
                'year' => $request->year,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => 'An unexpected error occurred during registration',
                'error_code' => 'REGISTRATION_ERROR'
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'error_code' => 'VALIDATION_ERROR'
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'error_code' => 'INVALID_CREDENTIALS'
                ], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => 'An unexpected error occurred during login',
                'error_code' => 'LOGIN_ERROR'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            if (!$request->user()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No authenticated user found',
                    'error_code' => 'NOT_AUTHENTICATED'
                ], 401);
            }

            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout successful'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => 'An unexpected error occurred during logout',
                'error_code' => 'LOGOUT_ERROR'
            ], 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            if (!$request->user()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No authenticated user found',
                    'error_code' => 'NOT_AUTHENTICATED'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $request->user()
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve profile',
                'error' => 'An unexpected error occurred while fetching profile',
                'error_code' => 'PROFILE_ERROR'
            ], 500);
        }
    }
}
