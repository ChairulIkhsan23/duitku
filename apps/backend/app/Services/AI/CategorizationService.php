<?php

namespace App\Services\AI;

use App\Models\Category;
use App\Models\KeywordMapping;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * CategorizationService
 * 
 * Mengelola kategorisasi otomatis transaksi menggunakan 2-layer approach:
 * Layer 1: Rule-based matching dengan keyword database (offline, gratis)
 * Layer 2: AI (Gemini) untuk fallback (online, berbayar, diperlambat cache)
 * 
 * Service ini membantu user mengkategorikan transaksi tanpa harus memilih kategori manual.
 */
class CategorizationService
{
    protected $apiKey;
    protected $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    /**
     * categorize
     * 
     * Fungsi utama untuk mengkategorikan transaksi.
     * Menggunakan 2-layer approach: rule-based dulu, kalau tidak cocok gunakan AI.
     * 
     * @param string $note Deskripsi/note transaksi yang akan dikategorikan
     * @param string $type Tipe transaksi (income/expense)
     * @return Category Kategori yang sesuai dengan transaksi
     */
    public function categorize(string $note, string $type): Category
    {
        // Jika note kosong, return kategori default 'Lain-lain'
        if (empty(trim($note))) {
            return Category::where('name', 'Lain-lain')
                ->where('type', $type)
                ->first() ?? $this->getFirstCategory($type);
        }

        // Layer 1: Rule-based matching (offline, instant, gratis)
        $category = $this->ruleBasedCategorization($note, $type);
        
        if ($category) {
            Log::info('AI: Rule-based match success', [
                'note' => $note,
                'category' => $category->name
            ]);
            return $category;
        }

        // Layer 2: AI Gemini (online, bayar/pakai quota, lebih lambat)
        Log::info('AI: No rule match, calling Gemini API', ['note' => $note]);
        return $this->aiCategorization($note, $type);
    }

    /**
     * ruleBasedCategorization
     * 
     * Layer 1: Pencocokan berdasarkan keyword database (offline).
     * Mencari keyword mapping yang aktif dan mencocokkan dengan note.
     * Sangat cepat dan tidak menggunakan API quota.
     * 
     * @param string $note Note transaksi yang akan dicocokkan
     * @param string $type Tipe transaksi (income/expense)
     * @return Category|null Kategori jika cocok, null jika tidak
     */
    private function ruleBasedCategorization(string $note, string $type): ?Category
    {
        // Normalize note ke lowercase untuk matching yang case-insensitive
        $note = strtolower($note);
        
        // Ambil semua mapping keyword yang aktif
        $mappings = KeywordMapping::where('is_active', true)->get();

        foreach ($mappings as $mapping) {
            // Decode keywords dari JSON ke array
            $keywords = is_string($mapping->keywords) 
                ? json_decode($mapping->keywords, true) 
                : $mapping->keywords;
                
            // Cek setiap keyword apakah ada di note
            foreach ($keywords as $keyword) {
                if (str_contains($note, strtolower($keyword))) {
                    // Cari kategori yang sesuai
                    $category = Category::where('name', $mapping->category_name)
                        ->where('type', $type)
                        ->first();
                    
                    if ($category) {
                        return $category;
                    }
                }
            }
        }

        return null;
    }

    /**
     * aiCategorization
     * 
     * Layer 2: Kategorisasi menggunakan Google Gemini AI.
     * Dipanggil jika rule-based tidak menemukan match.
     * Menggunakan cache 24 jam untuk mengurangi API calls.
     * 
     * @param string $note Note transaksi yang akan dikategorikan oleh AI
     * @param string $type Tipe transaksi (income/expense)
     * @return Category Kategori berdasarkan AI atau fallback kategori default
     */
    private function aiCategorization(string $note, string $type): Category
    {
        // Cek apakah API key tersedia
        if (!$this->apiKey) {
            Log::warning('AI: GEMINI_API_KEY tidak terkonfigurasi');
            return $this->getDefaultCategory($type);
        }

        // Ambil daftar kategori yang tersedia untuk tipe ini
        $categories = Category::where('type', $type)
            ->where('is_default', true)
            ->pluck('name')
            ->toArray();

        // Cache hasil AI selama 24 jam untuk note yang sama (untuk save quota)
        $cacheKey = "ai_categorize_{$type}_" . md5($note);
        
        $categoryName = Cache::remember($cacheKey, 86400, function () use ($note, $type, $categories) {
            // Buat prompt untuk Gemini
            $prompt = "Kategorikan transaksi ini ke dalam salah satu kategori: " . implode(', ', $categories) . 
                    "\nTransaksi: \"{$note}\"" .
                    "\nJenis: " . ($type === 'income' ? 'Pemasukan' : 'Pengeluaran') .
                    "\n\nReturn hanya nama kategori, tanpa penjelasan. Jika ragu, return 'Lain-lain'.";

            try {
                // Panggil API Gemini dengan SSL verification disabled (optional untuk dev)
                $response = Http::withOptions(['verify' => false])
                    ->timeout(10)
                    ->post($this->apiUrl . '?key=' . $this->apiKey, [
                        'contents' => [
                            ['parts' => [['text' => $prompt]]]
                        ]
                    ]);

                if (!$response->successful()) {
                    Log::error('AI: Gemini API error', ['status' => $response->status()]);
                    return 'Lain-lain';
                }

                // Extract hasil kategorisasi dari response
                $result = $response->json();
                $returned = trim($result['candidates'][0]['content']['parts'][0]['text'] ?? 'Lain-lain');
                
                // Validasi apakah kategori yang direturn valid
                $validCategories = Category::where('type', $type)->pluck('name')->toArray();
                return in_array($returned, $validCategories) ? $returned : 'Lain-lain';
                
            } catch (\Exception $e) {
                Log::error('AI: Gemini call failed', ['error' => $e->getMessage()]);
                return 'Lain-lain';
            }
        });

        // Cari kategori berdasarkan nama hasil AI
        $category = Category::where('name', $categoryName)
            ->where('type', $type)
            ->first();
            
        return $category ?? $this->getDefaultCategory($type);
    }

    /**
     * getDefaultCategory
     * 
     * Ambil kategori default 'Lain-lain' atau kategori pertama sebagai fallback.
     * 
     * @param string $type Tipe transaksi (income/expense)
     * @return Category Kategori default atau kategori pertama
     */
    private function getDefaultCategory(string $type): Category
    {
        $category = Category::where('name', 'Lain-lain')
            ->where('type', $type)
            ->first();
            
        return $category ?? $this->getFirstCategory($type);
    }

    /**
     * getFirstCategory
     * 
     * Ambil kategori pertama dari tipe tertentu sebagai fallback terakhir.
     * 
     * @param string $type Tipe transaksi (income/expense)
     * @return Category Kategori pertama yang ditemukan
     */
    private function getFirstCategory(string $type): Category
    {
        return Category::where('type', $type)->first();
    }
}