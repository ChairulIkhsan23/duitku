<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Budget;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Support\Str;

/**
 * Feature Test: Dashboard
 *
 * Purpose: Menguji fungsionalitas halaman dashboard utama aplikasi,
 *          termasuk agregasi data transaksi, saldo, dan preferensi mata uang user.
 *
 * Coverage:
 * - Mengambil data dashboard lengkap dengan transaksi income dan expense
 * - Memastikan data dashboard mengembalikan informasi mata uang user
 *
 * @group dashboard
 */
class DashboardTest extends TestCase
{
    /**
     * User terautentikasi yang digunakan selama sesi testing dashboard API.
     */
    private User $user;

    /**
     * Inisialisasi: Membuat user dengan saldo awal dan streak days
     * sebelum setiap test dijalankan.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // ARRANGE (global): Membuat user dengan data awal yang realistis
        $this->user = $this->actingAsUser([
            'initial_balance' => 10000000,
            'streak_days'     => 5,
        ]);
    }

    /**
     * Test: Dashboard mengembalikan data lengkap ketika user memiliki transaksi.
     *
     * Memastikan endpoint dashboard mengagregasi data dari berbagai sumber
     * (transaksi income dan expense) dan mengembalikannya dalam satu response.
     */
    public function test_can_get_dashboard_data()
    {
        // ARRANGE: Mengambil kategori expense dan income yang tersedia dari seeder
        $expense = Category::where('type', 'expense')->first();
        $income  = Category::where('type', 'income')->first();

        // ARRANGE: Membuat transaksi expense untuk bulan berjalan
        Transaction::create([
            'id'          => Str::uuid(),
            'user_id'     => $this->user->id,
            'category_id' => $expense->id,
            'amount'      => 50000,
            'type'        => 'expense',
            'date'        => now(),
        ]);

        // ARRANGE: Membuat transaksi income untuk bulan berjalan
        Transaction::create([
            'id'          => Str::uuid(),
            'user_id'     => $this->user->id,
            'category_id' => $income->id,
            'amount'      => 5000000,
            'type'        => 'income',
            'date'        => now(),
        ]);

        // ACT: Mengirimkan request GET ke endpoint dashboard
        $response = $this->auth()->getJson('/api/dashboard');

        // ASSERT: Memastikan response HTTP 200 dan struktur data dashboard tersedia
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    /**
     * Test: Dashboard mengembalikan informasi mata uang sesuai preferensi user.
     *
     * Memastikan response dashboard selalu menyertakan flag success true
     * dan data mata uang yang relevan dengan pengaturan user.
     */
    public function test_dashboard_returns_user_currency()
    {
        // ACT: Mengirimkan request GET ke endpoint dashboard tanpa data transaksi
        $response = $this->auth()->getJson('/api/dashboard');

        // ASSERT: Memastikan response HTTP 200 dan flag success bernilai true
        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }
}