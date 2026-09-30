<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Middleware\LoginRateLimit;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and create token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user exists and password is correct
        if (! $user || ! Hash::check($request->password, $user->password)) {
            // Trigger rate limiter on failed attempt
            $middleware = new LoginRateLimit();
            $middleware->onFailedAttempt($request);

            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if account is locked
        if ($user->isLocked()) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been locked due to too many failed login attempts. Please contact your administrator.',
                'locked' => true,
            ], 423);
        }

        // Check if account is active
        if (!$user->is_active || $user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is not active. Please contact your administrator.',
                'inactive' => true,
            ], 403);
        }

        // Check if password change is required
        if ($user->must_change_password) {
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Password change required',
                'data' => [
                    'user' => $user->load('roles'),
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'must_change_password' => true,
                ],
            ], 200);
        }

        // Clear rate limiter on successful login
        $middleware = new LoginRateLimit();
        $middleware->onSuccessfulLogin($request);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user->load('roles'),
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }

    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'pending',
            'is_active' => false,
        ]);

        // Assign default role
        $user->assignRole('viewer');

        return response()->json([
            'success' => true,
            'message' => 'Registration successful. Your account is pending approval.',
            'data' => [
                'user' => $user->load('roles'),
            ],
        ], 201);
    }

    /**
     * Logout user (revoke all tokens).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ], 200);
    }

    /**
     * Get the authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $request->user()->load('roles', 'permissions', 'group', 'groups', 'primaryBranch', 'district'),
            ],
        ], 200);
    }
}
