<?php
// app/Services/AI/InsightGeneratorService.php

namespace App\Services\AI;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * InsightGeneratorService
 * 
 * Menggunakan AI (Gemini) untuk generate insight keuangan personal untuk pengguna.
 * Service ini menganalisis data transaksi pengguna dan memberikan rekomendasi actionable.
 * 
 * Menggunakan cache 1 minggu untuk mengurangi API calls dan biaya.
 */
class InsightGeneratorService
{
    protected $apiKey;
    protected $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    /**
     * generate
     * 
     * Generate insight berdasarkan data keuangan user untuk periode tertentu.
     * Menggunakan cache untuk menghindari API calls berulang untuk periode yang sama.
     * 
     * @param User $user Pengguna yang insightnya akan di-generate
     * @param string $period Periode insight (daily, weekly, monthly)
     * @return array Array berisi insights, summary, dan recommendations
     */
    public function generate(User $user, string $period): array
    {
        // Cek API key
        if (!$this->apiKey) {
            Log::warning('AI Insight: GEMINI_API_KEY tidak terkonfigurasi');
            return $this->getFallbackInsight();
        }

        // Ambil data transaksi user untuk periode tertentu
        $data = $this->getUserData($user, $period);
        
        // Buat prompt untuk AI
        $prompt = $this->buildPrompt($user, $data, $period);
        
        // Cache hasil insight selama 1 minggu (86400 * 7 detik)
        $cacheKey = "ai_insight_{$user->id}_{$period}_" . now()->weekOfYear;
        
        return Cache::remember($cacheKey, 86400, function () use ($prompt) {
            return $this->callAI($prompt);
        });
    }

    /**
     * getUserData
     * 
     * Mengumpulkan data transaksi user untuk periode tertentu.
     * Termasuk total income/expense, savings rate, top categories, dan streak info.
     * 
     * @param User $user Pengguna yang datanya diambil
     * @param string $period Periode (daily, weekly, monthly)
     * @return array Array berisi aggregated data transaksi
     */
    private function getUserData(User $user, string $period): array
    {
        // Tentukan tanggal awal dan akhir berdasarkan periode
        [$startDate, $endDate] = $this->resolvePeriod($period);
        
        // Ambil transaksi dalam periode tersebut
        $transactions = $user->transactions()
            ->whereBetween('date', [$startDate, $endDate])
            ->with('category')
            ->get();
            
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        
        // Group pengeluaran berdasarkan kategori, ambil top 3
        $expenseByCategory = $transactions
            ->where('type', 'expense')
            ->groupBy('category.name')
            ->map(fn($group) => $group->sum('amount'))
            ->sortDesc()
            ->take(3)
            ->toArray();
            
        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $totalIncome - $totalExpense,
            'savings_rate' => $totalIncome > 0 ? (($totalIncome - $totalExpense) / $totalIncome) * 100 : 0,
            'top_expense_categories' => $expenseByCategory,
            'streak_days' => $user->streak_days,
            'transaction_count' => $transactions->count(),
        ];
    }

    /**
     * resolvePeriod
     * 
     * Mengkonversi string periode menjadi start dan end date.
     * 
     * @param string $period Periode (daily, weekly, monthly)
     * @return array Array berisi [$startDate, $endDate]
     */
    private function resolvePeriod(string $period): array
    {
        return match ($period) {
            'daily' => [now()->startOfDay(), now()->endOfDay()],
            'weekly' => [now()->startOfWeek(), now()->endOfWeek()],
            'monthly' => [now()->startOfMonth(), now()->endOfMonth()],
            default => [now()->startOfWeek(), now()->endOfWeek()],
        };
    }

    /**
     * buildPrompt
     * 
     * Membangun prompt yang akan dikirim ke Gemini AI.
     * Prompt berisi data finansial user dan instruksi untuk generate insight actionable.
     * 
     * @param User $user Pengguna pemilik data
     * @param array $data Data keuangan yang sudah diaggregasi
     * @param string $period Periode insight
     * @return string Prompt untuk Gemini
     */
    private function buildPrompt(User $user, array $data, string $period): string
    {
        // Format top categories untuk prompt
        $categoryList = '';
        foreach ($data['top_expense_categories'] as $cat => $amount) {
            $categoryList .= "- {$cat}: Rp " . number_format($amount, 0, ',', '.') . "\n";
        }

        return "Anda adalah asisten keuangan pribadi. Berdasarkan data keuangan user berikut, berikan 5 insight singkat dan actionable:

Nama: {$user->name}
Periode: {$period}
Total Pemasukan: Rp " . number_format($data['total_income'], 0, ',', '.') . "
Total Pengeluaran: Rp " . number_format($data['total_expense'], 0, ',', '.') . "
Tabungan: Rp " . number_format($data['balance'], 0, ',', '.') . "
Rate Menabung: {$data['savings_rate']}%
Streak Mencatat: {$data['streak_days']} hari
Top 3 Kategori Pengeluaran:
{$categoryList}

Format output JSON (JANGAN PAKAI MARKDOWN, murni JSON):
{
    \"insights\": [
        {\"type\": \"spending_alert\", \"message\": \"...\"},
        {\"type\": \"saving_tip\", \"message\": \"...\"},
        {\"type\": \"streak_motivation\", \"message\": \"...\"},
        {\"type\": \"budget_advice\", \"message\": \"...\"},
        {\"type\": \"achievement\", \"message\": \"...\"}
    ],
    \"summary\": \"1-2 kalimat ringkasan kondisi keuangan user\",
    \"recommendations\": [\"rekomendasi 1\", \"rekomendasi 2\", \"rekomendasi 3\"]
}";
    }

    /**
     * callAI
     * 
     * Memanggil API Gemini dengan prompt yang diberikan.
     * Handle response parsing dan error handling.
     * 
     * @param string $prompt Prompt untuk Gemini
     * @return array Array berisi insights, summary, dan recommendations
     */
    private function callAI(string $prompt): array
    {
        try {
            // Panggil API Gemini dengan SSL verification disabled
            $response = Http::withOptions(['verify' => false])
                ->timeout(15)
                ->post($this->apiUrl . '?key=' . $this->apiKey, [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ]
                ]);

            if (!$response->successful()) {
                Log::error('AI Insight: API error', ['status' => $response->status()]);
                return $this->getFallbackInsight();
            }

            // Extract hasil dari response
            $result = $response->json();
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            // Bersihkan response dari markdown JSON wrapper
            $text = preg_replace('/```json\n?/', '', $text);
            $text = preg_replace('/```\n?/', '', $text);
            $text = trim($text);
            
            // Parse JSON
            $decoded = json_decode($text, true);
            
            // Cek jika JSON parsing error
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('AI Insight: JSON decode error', ['error' => json_last_error_msg()]);
                return $this->getFallbackInsight();
            }
            
            // Merge dengan fallback untuk handle missing fields
            return array_merge($this->getFallbackInsight(), $decoded);
            
        } catch (\Exception $e) {
            Log::error('AI Insight: Call failed', ['error' => $e->getMessage()]);
            return $this->getFallbackInsight();
        }
    }

    /**
     * getFallbackInsight
     * 
     * Fallback insight jika AI gagal atau tidak terkonfigurasi.
     * Berisi generic insight yang tetap berguna untuk user.
     * 
     * @return array Array berisi generic insights
     */
    private function getFallbackInsight(): array
    {
        return [
            'insights' => [
                ['type' => 'general', 'message' => 'Terus konsisten mencatat keuangan ya!'],
                ['type' => 'general', 'message' => 'Dengan rutin mencatat, kamu lebih mudah mengontrol keuangan.'],
            ],
            'summary' => 'Terus konsisten mencatat transaksi untuk mendapatkan insight yang lebih personal.',
            'recommendations' => [
                'Catat transaksi setiap hari',
                'Buat budget bulanan', 
                'Pantau pengeluaran terbesar'
            ]
        ];
    }
}