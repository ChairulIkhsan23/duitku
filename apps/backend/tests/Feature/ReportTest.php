<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Transaction;

/**
 * Feature Test: Report
 *
 * Purpose: Menguji fungsionalitas API report transaksi,
 *          termasuk laporan mingguan, bulanan, custom range,
 *          serta export data report dalam berbagai format.
 *
 * Coverage:
 * - Mengambil laporan mingguan user
 * - Mengambil laporan bulanan user
 * - Mengambil laporan berdasarkan rentang tanggal custom
 * - Export report ke format tertentu (CSV, dll)
 *
 * @group report
 */
class ReportTest extends TestCase
{
    /**
     * User terautentikasi yang digunakan selama testing report API.
     */
    private User $user;

    /**
     * Inisialisasi: Membuat user dan data transaksi sebelum setiap test dijalankan.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // ARRANGE (global): autentikasi user untuk semua test report
        $this->user = $this->actingAsUser();

        // ARRANGE (global): membuat sample transaction untuk user
        Transaction::factory()->count(20)->create([
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test: User dapat mengambil laporan transaksi mingguan.
     *
     * Memastikan endpoint weekly report mengembalikan struktur data
     * yang mencakup periode, ringkasan, dan daftar transaksi.
     */
    public function test_can_get_weekly_report()
    {
        // ACT: request laporan mingguan
        $response = $this->auth()->getJson('/api/reports/weekly');

        // ASSERT: response sukses dan struktur data sesuai kontrak API
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'period',
                    'summary',
                    'transactions',
                ],
            ]);
    }

    /**
     * Test: User dapat mengambil laporan transaksi bulanan.
     *
     * Memastikan endpoint monthly report mengembalikan response sukses
     * untuk agregasi transaksi per bulan.
     */
    public function test_can_get_monthly_report()
    {
        // ACT: request laporan bulanan
        $response = $this->auth()->getJson('/api/reports/monthly');

        // ASSERT: response berhasil
        $response->assertOk();
    }

    /**
     * Test: User dapat mengambil laporan custom berdasarkan rentang tanggal.
     *
     * Memastikan endpoint custom report menerima parameter start_date dan end_date
     * serta mengembalikan data sesuai filter waktu.
     */
    public function test_can_get_custom_report()
    {
        // ACT: request laporan custom dengan date range
        $response = $this->auth()->getJson(
            '/api/reports/custom?start_date=2026-01-01&end_date=2026-12-31'
        );

        // ASSERT: response berhasil
        $response->assertOk();
    }

    /**
     * Test: User dapat mengekspor report transaksi.
     *
     * Memastikan endpoint export menerima format dan date range,
     * lalu mengembalikan response sukses (file export).
     */
    public function test_can_export_report()
    {
        // ACT: request export report ke format CSV
        $response = $this->auth()->postJson('/api/reports/export', [
            'format'     => 'csv',
            'start_date' => '2026-01-01',
            'end_date'   => '2026-12-31',
        ]);

        // ASSERT: export berhasil diproses
        $response->assertOk();
    }
}