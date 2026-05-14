<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Service untuk mengambil data dashboard
     */
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Ambil semua data dashboard user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Ambil data dashboard berdasarkan user login
        $data = $this->dashboardService->getDashboardData(
            $request->user()
        );

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}