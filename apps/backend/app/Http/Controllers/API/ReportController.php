<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Services\ReportService;
use App\Services\ExportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Service untuk generate laporan
     */
    protected $reportService;

    /**
     * Service untuk export laporan ke file
     */
    protected $exportService;

    /**
     * Inject ReportService dan ExportService
     *
     * @param ReportService $reportService
     * @param ExportService $exportService
     */
    public function __construct(ReportService $reportService, ExportService $exportService)
    {
        $this->reportService = $reportService;
        $this->exportService = $exportService;
    }

    /**
     * Generate laporan mingguan
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function weekly(Request $request)
    {
        // Rentang tanggal minggu ini
        $startDate = now()->startOfWeek()->format('Y-m-d');
        $endDate = now()->endOfWeek()->format('Y-m-d');
        
        // Generate report
        $report = $this->reportService->generateReport(
            $request->user(),
            $startDate,
            $endDate
        );
        
        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate laporan bulanan
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function monthly(Request $request)
    {
        // Rentang tanggal bulan ini
        $startDate = now()->startOfMonth()->format('Y-m-d');
        $endDate = now()->endOfMonth()->format('Y-m-d');
        
        // Generate report
        $report = $this->reportService->generateReport(
            $request->user(),
            $startDate,
            $endDate
        );
        
        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Generate laporan custom berdasarkan range tanggal
     *
     * @param ReportRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function custom(ReportRequest $request)
    {
        // Generate report berdasarkan input user
        $report = $this->reportService->generateReport(
            $request->user(),
            $request->start_date,
            $request->end_date
        );
        
        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Export laporan transaksi ke file (PDF/Excel/CSV)
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function export(Request $request)
    {
        // Validasi input export
        $request->validate([
            'format' => 'required|in:pdf,excel,csv',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        // Generate file export
        $content = $this->exportService->exportTransactions(
            $request->user(),
            $request->start_date,
            $request->end_date,
            $request->format
        );

        // Tentukan extension file
        $extension = match ($request->format) {
            'excel' => 'xlsx',
            default => $request->format
        };

        // Nama file hasil export
        $filename = "laporan_keuangan_{$request->start_date}_to_{$request->end_date}.{$extension}";

        return response($content)
            ->header('Content-Type', $this->getContentType($request->format))
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    /**
     * Mapping content type berdasarkan format export
     *
     * @param string $format
     * @return string
     */
    private function getContentType(string $format): string
    {
        return match($format) {
            'pdf' => 'application/pdf',
            'excel' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'csv' => 'text/csv',
            default => 'application/json'
        };
    }
}