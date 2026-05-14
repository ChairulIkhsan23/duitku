<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Str;

/**
 * Feature Test: Transaction
 *
 * Purpose: Menguji seluruh fungsionalitas API transaksi,
 *          termasuk CRUD transaksi, validasi input,
 *          akses kontrol user, serta summary data keuangan.
 *
 * Coverage:
 * - List transaksi user (dengan pagination)
 * - Create transaksi income & expense
 * - Validasi transaksi tidak valid
 * - Detail transaksi
 * - Authorization (akses transaksi user lain)
 * - Delete transaksi
 * - Summary transaksi berdasarkan kategori
 *
 * @group transaction
 */
class TransactionTest extends TestCase
{
    /**
     * Category untuk transaksi pengeluaran (expense).
     */
    private ?Category $expenseCategory;

    /**
     * Category untuk transaksi pemasukan (income).
     */
    private ?Category $incomeCategory;

    /**
     * Setup data sebelum setiap test dijalankan.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // ARRANGE (global): membuat user login untuk semua test
        $this->actingAsUser([
            'initial_balance' => 10000000,
            'streak_days' => 0,
        ]);

        // ARRANGE (global): ambil category dari seeder
        $this->expenseCategory = Category::where('type', 'expense')->first();
        $this->incomeCategory  = Category::where('type', 'income')->first();

        // ARRANGE (fallback): jika seeder tidak tersedia
        if (!$this->expenseCategory) {
            $this->expenseCategory = Category::create([
                'id' => Str::uuid(),
                'name' => 'Test Expense',
                'type' => 'expense',
                'icon' => 'FaTest',
                'color' => '#000000',
                'is_default' => true,
                'user_id' => null,
            ]);
        }

        if (!$this->incomeCategory) {
            $this->incomeCategory = Category::create([
                'id' => Str::uuid(),
                'name' => 'Test Income',
                'type' => 'income',
                'icon' => 'FaTest',
                'color' => '#000000',
                'is_default' => true,
                'user_id' => null,
            ]);
        }
    }

    /**
     * Test: User dapat melihat daftar transaksi miliknya.
     *
     * Memastikan API mengembalikan list transaksi dengan struktur data
     * yang sesuai termasuk pagination metadata.
     */
    public function test_can_get_transactions_list()
    {
        // ARRANGE: membuat data transaksi dummy
        Transaction::factory()
            ->count(5)
            ->create(['user_id' => $this->authUser->id]);

        // ACT: request list transaksi
        $response = $this->auth()->getJson('/api/transactions');

        // ASSERT: response dan struktur JSON sesuai
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'amount', 'type', 'date', 'category'],
                ],
                'links',
                'meta',
            ]);
    }

    /**
     * Test: User dapat melihat transaksi dengan pagination.
     *
     * Memastikan parameter per_page bekerja dengan benar.
     */
    public function test_can_get_transactions_list_with_pagination()
    {
        // ARRANGE: create banyak transaksi
        Transaction::factory()
            ->count(20)
            ->create(['user_id' => $this->authUser->id]);

        // ACT: request dengan pagination
        $response = $this->auth()->getJson('/api/transactions?per_page=10');

        // ASSERT: data terbatas sesuai pagination
        $response->assertOk()
            ->assertJsonCount(10, 'data');
    }

    /**
     * Test: User dapat membuat transaksi pengeluaran (expense).
     *
     * Memastikan transaksi tersimpan di database dan response valid.
     */
    public function test_can_create_expense_transaction()
    {
        // ACT: create expense transaction
        $response = $this->auth()->postJson('/api/transactions', [
            'amount' => 75000,
            'type' => 'expense',
            'category_id' => $this->expenseCategory->id,
            'note' => 'Makan siang di restoran',
            'date' => now()->format('Y-m-d'),
            'location_name' => 'Jakarta',
        ]);

        // ASSERT: response sukses dan data tersimpan
        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'amount', 'type', 'category'],
            ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->authUser->id,
            'amount' => 75000,
            'type' => 'expense',
        ]);
    }

    /**
     * Test: User dapat membuat transaksi pemasukan (income).
     *
     * Memastikan income transaction tersimpan dengan benar.
     */
    public function test_can_create_income_transaction()
    {
        // ACT: create income transaction
        $response = $this->auth()->postJson('/api/transactions', [
            'amount' => 5000000,
            'type' => 'income',
            'category_id' => $this->incomeCategory->id,
            'note' => 'Gaji bulanan',
            'date' => now()->format('Y-m-d'),
        ]);

        // ASSERT: response sukses
        $response->assertCreated()
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test: Validasi gagal ketika data transaksi tidak valid.
     *
     * Memastikan sistem menolak input negatif dan category tidak valid.
     */
    public function test_cannot_create_invalid_transaction()
    {
        // ACT: kirim data invalid
        $response = $this->auth()->postJson('/api/transactions', [
            'amount' => -10000,
            'type' => 'expense',
            'category_id' => 'invalid',
        ]);

        // ASSERT: validation error
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount', 'category_id']);
    }

    /**
     * Test: User dapat mengambil detail transaksi miliknya.
     */
    public function test_can_get_single_transaction()
    {
        // ARRANGE: create transaction
        $transaction = Transaction::factory()
            ->create(['user_id' => $this->authUser->id]);

        // ACT: request detail transaksi
        $response = $this->auth()
            ->getJson("/api/transactions/{$transaction->id}");

        // ASSERT: response sesuai struktur
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'amount', 'type', 'category'],
            ]);
    }

    /**
     * Test: User tidak dapat mengakses transaksi user lain.
     */
    public function test_cannot_access_other_users_transaction()
    {
        // ARRANGE: create user lain + transaction
        $otherUser = User::factory()->create();

        $transaction = Transaction::factory()
            ->create(['user_id' => $otherUser->id]);

        // ACT: coba akses transaksi orang lain
        $response = $this->auth()
            ->getJson("/api/transactions/{$transaction->id}");

        // ASSERT: forbidden access
        $response->assertForbidden();
    }

    /**
     * Test: User dapat menghapus transaksi miliknya.
     */
    public function test_can_delete_transaction()
    {
        // ARRANGE: create transaction
        $transaction = Transaction::factory()
            ->create(['user_id' => $this->authUser->id]);

        // ACT: delete request
        $response = $this->auth()
            ->deleteJson("/api/transactions/{$transaction->id}");

        // ASSERT: success delete + data hilang dari database
        $response->assertOk();

        $this->assertDatabaseMissing('transactions', [
            'id' => $transaction->id,
        ]);
    }

    /**
     * Test: User dapat melihat summary transaksi berdasarkan kategori.
     *
     * Memastikan agregasi income, expense, balance, dan statistik lainnya.
     */
    public function test_can_get_summary_by_category()
    {
        // ARRANGE: create sample transactions
        Transaction::factory()
            ->count(3)
            ->create(['user_id' => $this->authUser->id]);

        // ACT: request summary
        $response = $this->auth()
            ->getJson('/api/transactions/summary/by-category');

        // ASSERT: struktur summary sesuai
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_income',
                    'total_expense',
                    'balance',
                    'savings_rate',
                    'top_categories',
                    'transaction_count',
                ],
            ]);
    }
}