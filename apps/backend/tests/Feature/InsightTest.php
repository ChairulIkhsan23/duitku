<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Insight;

/**
 * Feature Test: Insight
 *
 * Purpose: Menguji fungsionalitas sistem insight keuangan pengguna,
 *          mencakup pengambilan daftar, insight terbaru, dan penandaan sebagai telah dibaca.
 *
 * Coverage:
 * - Mengambil daftar semua insight milik user
 * - Mengambil insight terbaru (latest)
 * - Menandai insight tertentu sebagai sudah dibaca
 *
 * @group insight
 */
class InsightTest extends TestCase
{
    /**
     * User terautentikasi yang digunakan selama sesi testing insight API.
     */
    private User $user;

    /**
     * Inisialisasi: Membuat dan mengautentikasi user sebelum setiap test dijalankan.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // ARRANGE (global): Membuat user terautentikasi untuk digunakan di semua test
        $this->user = $this->actingAsUser();
    }

    /**
     * Test: User dapat mengambil daftar semua insight keuangan miliknya.
     *
     * Memastikan endpoint mengembalikan koleksi insight yang
     * telah di-generate untuk user yang sedang terautentikasi.
     */
    public function test_can_get_insights_list()
    {
        // ARRANGE: Membuat 3 data insight untuk user yang terautentikasi
        Insight::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        // ACT: Mengirimkan request GET ke endpoint daftar insight
        $response = $this->auth()->getJson('/api/insights');

        // ASSERT: Memastikan response HTTP 200 dan struktur data insight tersedia
        $response->assertOk()
            ->assertJsonStructure([
                'data',
            ]);
    }

    /**
     * Test: User dapat mengambil insight terbaru yang di-generate sistem.
     *
     * Memastikan endpoint /insights/latest mengembalikan satu insight
     * paling terkini beserta data lengkapnya.
     */
    public function test_can_get_latest_insight()
    {
        // ACT: Mengirimkan request GET ke endpoint insight terbaru
        $response = $this->auth()->getJson('/api/insights/latest');

        // ASSERT: Memastikan response HTTP 200 dan struktur JSON insight sesuai
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    /**
     * Test: User dapat menandai insight sebagai sudah dibaca.
     *
     * Memastikan endpoint mark-as-read bekerja dengan benar dan
     * mengembalikan response sukses setelah status insight diperbarui.
     */
    public function test_can_mark_insight_as_read()
    {
        // ARRANGE: Membuat satu data insight yang belum dibaca untuk user
        $insight = Insight::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // ACT: Mengirimkan request POST ke endpoint read untuk insight tersebut
        $response = $this->auth()
            ->postJson("/api/insights/{$insight->id}/read");

        // ASSERT: Memastikan response HTTP 200 dan success bernilai true
        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }
}