<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\InsightResource;
use App\Services\InsightService;
use Illuminate\Http\Request;
use App\Jobs\ProcessInsightGeneration;

class InsightController extends Controller
{
    /**
     * Service untuk menangani logic insight
     */
    protected $insightService;

    /**
     * Inject InsightService ke controller
     *
     * @param InsightService $insightService
     */
    public function __construct(InsightService $insightService)
    {
        $this->insightService = $insightService;
    }

    /**
     * Ambil semua insight user (opsional filter type)
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        // Ambil insight milik user
        $insights = $this->insightService->getUserInsights(
            $request->user(),
            $request->type
        );
        
        return InsightResource::collection($insights);
    }

    /**
     * Ambil insight terbaru yang belum dibaca
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function latest(Request $request)
    {
        // Ambil insight unread terakhir
        $insight = $this->insightService->getLatestUnread($request->user());

        return response()->json([
            'success' => true,
            'data' => $insight ? new InsightResource($insight) : null
        ]);
    }

    /**
     * Tandai insight sebagai sudah dibaca
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id, Request $request)
    {
        // Update status insight menjadi read
        $this->insightService->markAsRead($request->user(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Insight marked as read'
        ]);
    }

    /**
     * Generate insight secara async (queue job)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(Request $request)
    {
        // Ambil period type (default weekly)
        $periodType = $request->input('period_type', 'weekly');

        // Dispatch job untuk generate insight
        ProcessInsightGeneration::dispatch($request->user(), $periodType);

        return response()->json([
            'success' => true,
            'message' => "Insight generation ($periodType) has been queued.",
            'period_type' => $periodType
        ]);
    }
}