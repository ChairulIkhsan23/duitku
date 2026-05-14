<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Service untuk menangani logic autentikasi
     */
    protected $authService;

    /**
     * Inject AuthService ke controller
     *
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register user baru
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        // Proses registrasi user
        $result = $this->authService->register($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => new UserResource($result['user']),
                'token' => $result['token']
            ]
        ], 201);
    }

    /**
     * Login user dan generate token
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        // Proses login user
        $result = $this->authService->login($request->email, $request->password);
        
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource($result['user']),
                'token' => $result['token']
            ]
        ]);
    }

    /**
     * Logout user yang sedang login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Hapus token user
        $this->authService->logout($request->user());
        
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Ambil data user yang sedang login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user())
        ]);
    }
}