<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function csrfCookie(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'CSRF cookie set',
        ]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->login(
                $request->validated('email'),
                $request->validated('password')
            );

            return response()->json([
                'data' => new UserResource($user),
                'message' => 'Login successful',
            ]);
        } catch (\Exception $e) {
            Log::error('Login failed', [
                'email' => $request->validated('email'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Invalid credentials',
                'errors' => [
                    'email' => ['The provided credentials are incorrect.'],
                ],
            ], 401);
        }
    }

    public function register(Request $request): JsonResponse
    {
        if (! config('app.feature_registration_enabled', false)) {
            return response()->json([
                'message' => 'Registration is disabled',
            ], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $this->authService->register($validated);

        return response()->json([
            'data' => new UserResource($user),
            'message' => 'Registration successful',
        ], 201);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($request->user()),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}

