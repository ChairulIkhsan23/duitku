<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

/**
 * Feature Test: Badge
 *
 * Purpose: Menguji fungsionalitas sistem badge/penghargaan pengguna,
 *          termasuk badge yang telah diraih maupun seluruh badge yang tersedia.
 *
 * Coverage:
 * - Mengambil daftar badge yang dimiliki oleh user
 * - Mengambil seluruh badge beserta status perolehannya
 *
 * @group badge
 */
class BadgeTest extends TestCase
{
    /**
     * User terautentikasi yang digunakan selama sesi testing badge API.
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
     * Test: User dapat mengambil daftar badge yang telah berhasil diraih.
     *
     * Memastikan bahwa endpoint badge mengembalikan koleksi badge
     * milik user yang sedang terautentikasi.
     */
    public function test_can_get_user_badges()
    {
        // ACT: Mengirimkan request GET ke endpoint daftar badge user
        $response = $this->auth()->getJson('/api/badges');

        // ASSERT: Memastikan response HTTP 200 dan struktur data badge tersedia
        $response->assertOk()
            ->assertJsonStructure([
                'data',
            ]);
    }

    /**
     * Test: User dapat melihat semua badge yang ada beserta status perolehannya.
     *
     * Memastikan bahwa endpoint ini mengembalikan seluruh badge dalam sistem,
     * baik yang sudah diraih maupun yang belum, beserta informasi statusnya.
     */
    public function test_can_get_all_badges_with_status()
    {
        // ACT: Mengirimkan request GET ke endpoint semua badge dengan status
        $response = $this->auth()->getJson('/api/badges/all');

        // ASSERT: Memastikan response HTTP 200 dan seluruh data badge tersedia
        $response->assertOk()
            ->assertJsonStructure([
                'data',
            ]);
    }
}