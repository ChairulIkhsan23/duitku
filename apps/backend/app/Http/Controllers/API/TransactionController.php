<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Services\TransactionService;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TransactionController extends Controller
{
    use AuthorizesRequests;

    /**
     * Service untuk menangani logic transaksi
     */
    protected TransactionService $transactionService;

    /**
     * Inject TransactionService ke controller
     *
     * @param TransactionService $transactionService
     */
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Ambil semua transaksi user (dengan pagination)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Query transaksi user dengan relasi kategori
        $transactions = $request->user()
            ->transactions()
            ->with('category')
            ->orderBy('date', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => TransactionResource::collection($transactions),
            'links' => [
                'first' => $transactions->url(1),
                'last' => $transactions->url($transactions->lastPage()),
                'prev' => $transactions->previousPageUrl(),
                'next' => $transactions->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ]
        ]);
    }

    /**
     * Simpan transaksi baru
     *
     * @param TransactionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TransactionRequest $request)
    {
        // Buat transaksi melalui service
        $transaction = $this->transactionService->createTransaction(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully',
            'data' => new TransactionResource($transaction)
        ], 201);
    }

    /**
     * Tampilkan detail transaksi
     *
     * @param Transaction $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Transaction $transaction)
    {
        // Validasi hak akses user
        $this->authorize('view', $transaction);

        return response()->json([
            'success' => true,
            'data' => new TransactionResource(
                $transaction->load('category')
            )
        ]);
    }

    /**
     * Update data transaksi
     *
     * @param TransactionRequest $request
     * @param Transaction $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TransactionRequest $request, Transaction $transaction)
    {
        // Validasi hak akses user
        $this->authorize('update', $transaction);

        // Update transaksi melalui service
        $transaction = $this->transactionService->updateTransaction(
            $transaction,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Transaction updated successfully',
            'data' => new TransactionResource($transaction)
        ]);
    }

    /**
     * Hapus transaksi
     *
     * @param Transaction $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Transaction $transaction)
    {
        // Validasi hak akses user
        $this->authorize('delete', $transaction);

        // Hapus transaksi via service
        $this->transactionService->deleteTransaction($transaction);

        return response()->json([
            'success' => true,
            'message' => 'Transaction deleted successfully'
        ]);
    }

    /**
     * Ringkasan transaksi berdasarkan kategori
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function summaryByCategory(Request $request)
    {
        // Ambil summary berdasarkan periode
        $summary = $this->transactionService->getSummary(
            $request->user(),
            $request->period ?? 'monthly'
        );

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }
}