<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BadgeResource;
use App\Http\Resources\BadgeWithStatusResource;
use App\Services\BadgeService;
use Illuminate\Http\Request;
use App\Models\Badge;

class BadgeController extends Controller
{
    /**
     * Service untuk menangani logic badge
     */
    protected $badgeService;

    /**
     * Inject BadgeService ke controller
     *
     * @param BadgeService $badgeService
     */
    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    /**
     * Ambil badge yang sudah dimiliki user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Ambil badge user dari service
        $badges = $this->badgeService->getUserBadges($request->user());
        
        return response()->json([
            'success' => true,
            'data' => BadgeResource::collection($badges)
        ]);
    }

    /**
     * Ambil semua badge beserta status kepemilikan user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function allBadges(Request $request)
    {
        // User yang sedang login
        $user = $request->user();

        // Ambil semua badge
        $badges = Badge::query()->get();

        // Ambil ID badge yang sudah dimiliki user
        $ownedBadgeIds = $user->badges()
            ->pluck('badges.id')
            ->flip();

        // Tambahkan status apakah badge sudah dimiliki atau belum
        $badges->each(function ($badge) use ($ownedBadgeIds) {
            $badge->is_earned = isset($ownedBadgeIds[$badge->id]);
        });

        return response()->json([
            'success' => true,
            'data' => BadgeWithStatusResource::collection($badges)
        ]);
    }
}