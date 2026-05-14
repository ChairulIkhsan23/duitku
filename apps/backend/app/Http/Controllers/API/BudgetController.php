<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BudgetFilterRequest;
use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Http\Resources\BudgetResource;
use App\Services\BudgetService;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BudgetController extends Controller
{
    use AuthorizesRequests;

    /**
     * Service untuk menangani logic budget
     */
    protected $budgetService;

    /**
     * Inject BudgetService ke controller
     *
     * @param BudgetService $budgetService
     */
    public function __construct(BudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    /**
     * Ambil semua budget user (dengan filter opsional bulan)
     *
     * @param BudgetFilterRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(BudgetFilterRequest $request)
    {
        // Query budget milik user
        $query = $request->user()
            ->budgets()
            ->with('category');

        // Filter berdasarkan bulan jika ada
        if ($request->month_year) {
            $month = \Carbon\Carbon::createFromFormat('Y-m', $request->month_year)->startOfMonth();
            $query->whereYear('month_year', $month->year)
                ->whereMonth('month_year', $month->month);
        }

        return BudgetResource::collection($query->get());
    }

    /**
     * Ambil status budget bulan berjalan
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function currentMonth(Request $request)
    {
        // Ambil status budget user dari service
        $status = $this->budgetService->getUserBudgetStatus($request->user());

        return response()->json([
            'success' => true,
            'data' => [
                'overall' => $status['overall'],
                'budgets' => BudgetResource::collection($status['budgets'])
            ]
        ]);
    }

    /**
     * Simpan budget baru
     *
     * @param StoreBudgetRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreBudgetRequest $request)
    {
        // Buat budget baru via service
        $budget = $this->budgetService->createBudget(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Budget created successfully',
            'data' => new BudgetResource($budget)
        ], 201);
    }

    /**
     * Tampilkan detail budget
     *
     * @param Budget $budget
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Budget $budget)
    {
        // Validasi hak akses user
        $this->authorize('view', $budget);

        return response()->json([
            'success' => true,
            'data' => new BudgetResource($budget->load('category'))
        ]);
    }

    /**
     * Update data budget
     *
     * @param UpdateBudgetRequest $request
     * @param Budget $budget
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateBudgetRequest $request, Budget $budget)
    {
        // Validasi hak akses user
        $this->authorize('update', $budget);

        // Update data budget
        $budget->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Budget updated successfully',
            'data' => new BudgetResource($budget->load('category'))
        ]);
    }

    /**
     * Hapus budget
     *
     * @param Budget $budget
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Budget $budget)
    {
        // Validasi hak akses user
        $this->authorize('delete', $budget);

        // Hapus budget
        $budget->delete();

        return response()->json([
            'success' => true,
            'message' => 'Budget deleted successfully'
        ]);
    }
}