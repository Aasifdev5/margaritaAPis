<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
   public function register(Request $request)
{
    try {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'whatsapp_number' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'whatsapp_number' => $request->whatsapp_number,
        ]);

        if (!$user) {
            Log::error('Failed to create user', $request->all());
            return response()->json(['message' => 'Failed to create user'], 500);
        }

        try {
            if (!\Schema::hasTable('personal_access_tokens')) {
                Log::error('personal_access_tokens table not found');
                return response()->json(['message' => 'Token table not found'], 500);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            Log::info('Token created for user ID: ' . $user->id, ['token' => $token]);
        } catch (\Exception $e) {
            Log::error('Token creation failed for user ID: ' . $user->id, ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Token creation failed', 'error' => $e->getMessage()], 500);
        }

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    } catch (\Exception $e) {
        Log::error('Registration error', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Registration failed', 'error' => $e->getMessage()], 500);
    }
}

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $user = Auth::user();
            if (!$user) {
                Log::error('Authenticated user not found');
                return response()->json(['message' => 'User not found'], 401);
            }

            try {
                // Verify Sanctum table exists
                if (!\Schema::hasTable('personal_access_tokens')) {
                    Log::error('personal_access_tokens table not found');
                    return response()->json(['message' => 'Token table not found'], 500);
                }

                $token = $user->createToken('auth_token')->plainTextToken;
                Log::info('Token created for user ID: ' . $user->id, ['token' => $token]);
            } catch (\Exception $e) {
                Log::error('Token creation failed for user ID: ' . $user->id, ['error' => $e->getMessage()]);
                return response()->json(['message' => 'Token creation failed', 'error' => $e->getMessage()], 500);
            }

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            Log::error('Login error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Login failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if (!$user) {
                Log::warning('No authenticated user found for logout');
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            // Revoke the current token
            $request->user()->currentAccessToken()->delete();
            Log::info('User logged out', ['user_id' => $user->id]);

            return response()->json(['message' => 'Logged out successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Logout error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Logout failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function testToken(Request $request)
    {
        try {
            $user = User::first();
            if (!$user) {
                Log::warning('No users found for test token');
                return response()->json(['message' => 'No users found'], 404);
            }

            if (!\Schema::hasTable('personal_access_tokens')) {
                Log::error('personal_access_tokens table not found');
                return response()->json(['message' => 'Token table not found'], 500);
            }

            $token = $user->createToken('test_token')->plainTextToken;
            Log::info('Test token created for user ID: ' . $user->id, ['token' => $token]);
            return response()->json(['token' => $token]);
        } catch (\Exception $e) {
            Log::error('Test token creation failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Token creation failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function user(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }
            return response()->json($user);
        } catch (\Exception $e) {
            Log::error('User fetch error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to fetch user', 'error' => $e->getMessage()], 500);
        }
    }
}
