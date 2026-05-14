<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Service untuk menangani logic notification
     */
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Ambil semua notifikasi user (dengan pagination)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Ambil notifikasi user dengan pagination
        $notifications = $this->notificationService
            ->getUserNotifications(
                $request->user(),
                $request->per_page ?? 20
            );

        return response()->json([
            'success' => true,
            'data' => NotificationResource::collection($notifications),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ]
        ]);
    }

    /**
     * Ambil notifikasi yang belum dibaca
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unread(Request $request)
    {
        // Ambil notifikasi unread user
        $notifications = $this->notificationService
            ->getUserUnreadNotifications($request->user());

        return response()->json([
            'success' => true,
            'data' => NotificationResource::collection($notifications),
        ]);
    }

    /**
     * Ambil jumlah notifikasi yang belum dibaca
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreadCount(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'count' => $this->notificationService
                    ->getUnreadCount($request->user())
            ]
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request, $id)
    {
        // Update status notifikasi menjadi read
        $this->notificationService->markAsRead(
            $request->user(),
            $id
        );

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Tandai semua notifikasi sebagai sudah dibaca
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead(Request $request)
    {
        // Update semua notifikasi user menjadi read
        $this->notificationService->markAllAsRead($request->user());

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }
}