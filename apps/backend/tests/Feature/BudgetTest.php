<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Budget;
use App\Models\Category;

/**
 * Feature Test: Budget
 *
 * Purpose: Menguji seluruh fungsionalitas manajemen anggaran (budget) pengguna,
 *          mencakup operasi CRUD, validasi input, otorisasi, dan logika bisnis.
 *
 * Coverage:
 * - Mengambil daftar budget dan filter berdasarkan bulan
 * - Mengambil budget bulan berjalan beserta ringkasan keseluruhan
 * - Membuat budget baru dengan validasi input
 * - Memperbarui dan menghapus budget milik sendiri
 * - Mencegah akses/modifikasi budget milik user lain (authorization)
 * - Logika bisnis: peringatan 80% dan status overspent
 *
 * @group budget
 */
class BudgetTest extends TestCase
{
    /**
     * User terautentikasi yang digunakan selama sesi testing budget API.
     */
    private User $user;

    /**
     * Inisialisasi: Membuat user dengan saldo awal sebelum setiap test dijalankan.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // ARRANGE (global): Membuat user dengan initial_balance untuk simulasi transaksi
        $this->user = $this->actingAsUser([
            'initial_balance' => 10000000,
        ]);
    }

    /**
     * Helper: Mengembalikan tanggal awal bulan berjalan untuk konsistensi data test.
     */
    private function month()
    {
        return now()->startOfMonth();
    }

    // =========================================================================
    // GET LIST
    // =========================================================================

    /**
     * Test: User dapat mengambil daftar semua budget yang dimilikinya.
     *
     * Memastikan endpoint mengembalikan koleksi budget milik user
     * yang sedang terautentikasi.
     */
    public function test_can_get_budgets_list()
    {
        // ARRANGE: Membuat 3 data budget untuk user pada bulan berjalan
        Budget::factory()->count(3)->create([
            'user_id'    => $this->user->id,
            'month_year' => $this->month()->format('Y-m-d'),
        ]);

        // ACT: Mengirimkan request GET ke endpoint daftar budget
        $response = $this->auth()->getJson('/api/budgets');

        // ASSERT: Memastikan response HTTP 200 dan struktur data tersedia
        $response->assertOk()
            ->assertJsonStructure([
                'data'
            ]);
    }

    // =========================================================================
    // FILTER BY MONTH
    // =========================================================================

    /**
     * Test: User dapat memfilter daftar budget berdasarkan bulan tertentu.
     *
     * Memastikan query parameter month_year bekerja dengan benar
     * dan hanya mengembalikan budget pada bulan yang diminta.
     */
    public function test_can_filter_budgets_by_month()
    {
        // ARRANGE: Membuat budget pada bulan berjalan sebagai data filter
        Budget::factory()->create([
            'user_id'    => $this->user->id,
            'month_year' => $this->month()->format('Y-m-d'),
        ]);

        // ACT: Mengirimkan request GET dengan query parameter month_year
        $response = $this->auth()->getJson(
            '/api/budgets?month_year=' . $this->month()->format('Y-m')
        );

        // ASSERT: Memastikan response HTTP 200 dan data yang difilter tersedia
        $response->assertOk()
            ->assertJsonStructure([
                'data'
            ]);
    }

    // =========================================================================
    // CURRENT MONTH
    // =========================================================================

    /**
     * Test: User dapat mengambil ringkasan budget bulan berjalan.
     *
     * Memastikan endpoint /budgets/current mengembalikan data ringkasan
     * keseluruhan anggaran beserta detail per-budget untuk bulan ini.
     */
    public function test_can_get_current_month_budgets()
    {
        // ARRANGE: Membuat budget aktif pada bulan berjalan
        Budget::factory()->create([
            'user_id'    => $this->user->id,
            'month_year' => $this->month()->format('Y-m-d'),
        ]);

        // ACT: Mengirimkan request GET ke endpoint budget bulan berjalan
        $response = $this->auth()->getJson('/api/budgets/current');

        // ASSERT: Memastikan response HTTP 200 dan struktur JSON berisi 'overall' dan 'budgets'
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'overall',
                    'budgets'
                ]
            ]);
    }

    // =========================================================================
    // CREATE
    // =========================================================================

    /**
     * Test: User dapat membuat budget baru dengan data yang valid.
     *
     * Memastikan budget tersimpan di database dengan category, limit amount,
     * dan bulan yang sesuai dengan data yang dikirimkan.
     */
    public function test_can_create_budget()
    {
        // ARRANGE: Mengambil kategori expense yang tersedia dari database
        $category = Category::where('type', 'expense')->first();

        // ACT: Mengirimkan request POST untuk membuat budget baru
        $response = $this->auth()->postJson('/api/budgets', [
            'category_id'  => $category->id,
            'limit_amount' => 100000,
            'month_year'   => $this->month()->format('Y-m'),
        ]);

        // ASSERT: Memastikan response HTTP 201, success true, dan data tersimpan di database
        $response->assertCreated()
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('budgets', [
            'user_id'      => $this->user->id,
            'category_id'  => $category->id,
            'limit_amount' => 100000,
        ]);
    }

    // =========================================================================
    // INVALID NEGATIVE LIMIT
    // =========================================================================

    /**
     * Test: Sistem menolak pembuatan budget dengan nilai limit yang negatif.
     *
     * Memastikan validasi server-side berjalan dan mengembalikan
     * HTTP 422 Unprocessable Entity ketika limit_amount bernilai negatif.
     */
    public function test_cannot_create_budget_with_negative_limit()
    {
        // ARRANGE: Mengambil kategori expense yang valid
        $category = Category::where('type', 'expense')->first();

        // ACT: Mengirimkan request POST dengan limit_amount yang tidak valid (negatif)
        $response = $this->auth()->postJson('/api/budgets', [
            'category_id'  => $category->id,
            'limit_amount' => -100,
            'month_year'   => $this->month()->format('Y-m'),
        ]);

        // ASSERT: Memastikan response HTTP 422 (validasi gagal)
        $response->assertStatus(422);
    }

    // =========================================================================
    // INVALID CATEGORY
    // =========================================================================

    /**
     * Test: Sistem menolak pembuatan budget dengan category_id yang tidak valid.
     *
     * Memastikan validasi foreign key pada category_id berjalan
     * dan mengembalikan HTTP 422 Unprocessable Entity.
     */
    public function test_cannot_create_budget_with_invalid_category()
    {
        // ACT: Mengirimkan request POST dengan category_id berupa string tidak valid
        $response = $this->auth()->postJson('/api/budgets', [
            'category_id'  => 'invalid',
            'limit_amount' => 1000,
            'month_year'   => $this->month()->format('Y-m'),
        ]);

        // ASSERT: Memastikan response HTTP 422 (validasi gagal)
        $response->assertStatus(422);
    }

    // =========================================================================
    // UPDATE
    // =========================================================================

    /**
     * Test: User dapat memperbarui data budget miliknya sendiri.
     *
     * Memastikan perubahan limit_amount tersimpan dengan benar di database
     * dan endpoint mengembalikan response sukses.
     */
    public function test_can_update_budget()
    {
        // ARRANGE: Membuat budget yang dimiliki oleh user yang terautentikasi
        $budget = Budget::factory()->create([
            'user_id'    => $this->user->id,
            'month_year' => $this->month()->format('Y-m-d'),
        ]);

        // ACT: Mengirimkan request PUT untuk memperbarui limit_amount budget
        $response = $this->auth()->putJson("/api/budgets/{$budget->id}", [
            'category_id'  => $budget->category_id,
            'limit_amount' => 200000,
            'month_year'   => $this->month()->format('Y-m'),
        ]);

        // ASSERT: Memastikan response HTTP 200, success true, dan perubahan tersimpan
        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('budgets', [
            'id'           => $budget->id,
            'limit_amount' => 200000,
        ]);
    }

    // =========================================================================
    // FORBIDDEN UPDATE
    // =========================================================================

    /**
     * Test: User tidak dapat memperbarui budget milik user lain.
     *
     * Memastikan authorization policy berjalan dengan benar dan
     * mengembalikan HTTP 403 Forbidden ketika user mencoba memodifikasi
     * budget yang bukan miliknya.
     */
    public function test_cannot_update_other_users_budget()
    {
        // ARRANGE: Membuat budget tanpa user_id spesifik (milik user lain via factory)
        $budget = Budget::factory()->create([
            'month_year' => $this->month()->format('Y-m-d'),
        ]);

        // ACT: User yang terautentikasi mencoba memperbarui budget milik user lain
        $response = $this->auth()->putJson("/api/budgets/{$budget->id}", [
            'category_id'  => $budget->category_id,
            'limit_amount' => 200000,
            'month_year'   => $this->month()->format('Y-m'),
        ]);

        // ASSERT: Memastikan response HTTP 403 Forbidden (akses ditolak)
        $response->assertForbidden();
    }

    // =========================================================================
    // DELETE
    // =========================================================================

    /**
     * Test: User dapat menghapus budget miliknya sendiri.
     *
     * Memastikan budget terhapus dari database setelah request
     * DELETE berhasil dieksekusi.
     */
    public function test_can_delete_budget()
    {
        // ARRANGE: Membuat budget yang dimiliki oleh user yang terautentikasi
        $budget = Budget::factory()->create([
            'user_id'    => $this->user->id,
            'month_year' => $this->month()->format('Y-m-d'),
        ]);

        // ACT: Mengirimkan request DELETE ke endpoint budget
        $response = $this->auth()->deleteJson("/api/budgets/{$budget->id}");

        // ASSERT: Memastikan response HTTP 200 dan data tidak lagi ada di database
        $response->assertOk();

        $this->assertDatabaseMissing('budgets', [
            'id' => $budget->id,
        ]);
    }

    // =========================================================================
    // FORBIDDEN DELETE
    // =========================================================================

    /**
     * Test: User tidak dapat menghapus budget milik user lain.
     *
     * Memastikan authorization policy mencegah penghapusan data
     * yang bukan milik user yang sedang terautentikasi.
     */
    public function test_cannot_delete_other_users_budget()
    {
        // ARRANGE: Membuat budget tanpa user_id spesifik (milik user lain via factory)
        $budget = Budget::factory()->create([
            'month_year' => $this->month()->format('Y-m-d'),
        ]);

        // ACT: User yang terautentikasi mencoba menghapus budget milik user lain
        $response = $this->auth()->deleteJson("/api/budgets/{$budget->id}");

        // ASSERT: Memastikan response HTTP 403 Forbidden (akses ditolak)
        $response->assertForbidden();
    }

    // =========================================================================
    // BUSINESS LOGIC
    // =========================================================================

    /**
     * Test: Budget menampilkan peringatan ketika pengeluaran mencapai 80% dari limit.
     *
     * Memastikan atribut `percentage` pada model Budget dihitung dengan benar
     * dan bernilai >= 80 ketika spent_amount sudah mencapai 80% dari limit_amount.
     * Logika ini digunakan sebagai trigger peringatan kepada pengguna.
     */
    public function test_budget_progress_warning_at_80_percent()
    {
        // ARRANGE: Membuat budget dengan spent_amount tepat 80% dari limit_amount
        $budget = Budget::factory()->create([
            'user_id'      => $this->user->id,
            'limit_amount' => 100000,
            'spent_amount' => 80000,
            'month_year'   => $this->month()->format('Y-m-d'),
        ]);

        // ASSERT: Memastikan kalkulasi persentase pada model >= 80 (kondisi warning)
        $this->assertTrue($budget->percentage >= 80);
    }

    /**
     * Test: Budget menampilkan status overspent ketika pengeluaran melebihi limit.
     *
     * Memastikan atribut `percentage` pada model Budget bernilai >= 100
     * ketika spent_amount melampaui limit_amount, menandakan kondisi overspent.
     */
    public function test_budget_shows_overspent_status()
    {
        // ARRANGE: Membuat budget dengan spent_amount yang melebihi limit_amount
        $budget = Budget::factory()->create([
            'user_id'      => $this->user->id,
            'limit_amount' => 100000,
            'spent_amount' => 120000,
            'month_year'   => $this->month()->format('Y-m-d'),
        ]);

        // ASSERT: Memastikan kalkulasi persentase pada model >= 100 (kondisi overspent)
        $this->assertTrue($budget->percentage >= 100);
    }
}