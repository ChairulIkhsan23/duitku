<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * AuthService instance
     */
    protected AuthService $authService;

    /**
     * Create a new controller instance.
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user
     * 
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->register($request->validated());

            // Auto login setelah register
            $data = $this->authService->login($user->email, $request->password);

            return response()->json([
                'message' => 'Registrasi berhasil',
                'data' => [
                    'user' => new UserResource($data['user']),
                    'token' => $data['token'],
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registrasi gagal',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Login user
     * 
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = $this->authService->login(
                $request->email,
                $request->password
            );

            return response()->json([
                'message' => 'Login berhasil',
                'data' => [
                    'user' => new UserResource($data['user']),
                    'token' => $data['token'],
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Login gagal',
                'errors' => $e->errors(),
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login gagal',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get current authenticated user
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $user = $this->authService->getCurrentUser($request->user());

        return response()->json([
            'message' => 'Data user berhasil diambil',
            'data' => new UserResource($user),
        ], 200);
    }

    /**
     * Logout user
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request->user());

            return response()->json([
                'message' => 'Logout berhasil',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout gagal',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
