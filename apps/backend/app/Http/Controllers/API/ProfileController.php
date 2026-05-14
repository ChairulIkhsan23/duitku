<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Service untuk menangani logic autentikasi & profil user
     */
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Ambil data profile user yang sedang login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user())
        ]);
    }

    /**
     * Update data profile user
     *
     * @param UpdateProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProfileRequest $request)
    {
        // Update profile melalui service
        $user = $this->authService->updateProfile(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Update pengaturan user (theme, language, dll)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSettings(Request $request)
    {
        // Validasi input settings
        $request->validate([
            'theme' => 'nullable|in:light,dark',
            'language' => 'nullable|string|max:5',
            'notifications_enabled' => 'nullable|boolean',
            'daily_reminder' => 'nullable|boolean',
        ]);

        $user = $request->user();

        // Merge settings lama dengan yang baru
        $settings = array_merge(
            $user->settings ?? [
                'theme' => 'light',
                'language' => 'id',
                'notifications_enabled' => true,
                'daily_reminder' => true,
            ],
            $request->only([
                'theme',
                'language',
                'notifications_enabled',
                'daily_reminder'
            ])
        );

        // Simpan settings ke database
        $user->update(['settings' => $settings]);

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => $settings
        ]);
    }

    /**
     * Update token notifikasi user (push notification)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateNotificationToken(Request $request)
    {
        // Validasi token notifikasi
        $request->validate([
            'notification_token' => 'required|string|max:255'
        ]);

        // Update token di user
        $request->user()->update([
            'notification_token' => $request->notification_token
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification token updated successfully'
        ]);
    }
}