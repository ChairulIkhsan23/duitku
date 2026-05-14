<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

/**
 * Feature Test: Profile
 *
 * Purpose: Menguji fungsionalitas manajemen profil pengguna,
 *          termasuk pengambilan data profil, pembaruan informasi pribadi,
 *          dan pembaruan pengaturan aplikasi.
 *
 * Coverage:
 * - Mengambil data profil lengkap user yang terautentikasi
 * - Memperbarui informasi pribadi (nama, mata uang)
 * - Memperbarui pengaturan preferensi aplikasi (notifikasi, tema)
 *
 * @group profile
 */
class ProfileTest extends TestCase
{
    /**
     * User terautentikasi yang digunakan selama sesi testing profile API.
     */
    private User $user;

    /**
     * Inisialisasi: Membuat user dengan settings kosong sebelum setiap test dijalankan.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // ARRANGE (global): Membuat user dengan settings array kosong sebagai state awal
        $this->user = $this->actingAsUser([
            'settings' => [],
        ]);
    }

    /**
     * Test: User dapat mengambil data profil lengkapnya.
     *
     * Memastikan endpoint mengembalikan informasi profil yang komprehensif
     * mencakup identitas, mata uang, dan data streak pengguna.
     */
    public function test_can_get_profile()
    {
        // ACT: Mengirimkan request GET ke endpoint profil user
        $response = $this->auth()->getJson('/api/profile');

        // ASSERT: Memastikan response HTTP 200 dan struktur JSON profil sesuai
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'currency_code',
                    'streak_days',
                ],
            ]);
    }

    /**
     * Test: User dapat memperbarui informasi profil pribadinya.
     *
     * Memastikan perubahan nama dan kode mata uang tersimpan dengan
     * benar di database dan endpoint mengembalikan response sukses.
     */
    public function test_can_update_profile()
    {
        // ACT: Mengirimkan request PUT untuk memperbarui nama dan mata uang user
        $response = $this->auth()->putJson('/api/profile', [
            'name'          => 'Updated Name',
            'currency_code' => 'USD',
        ]);

        // ASSERT: Memastikan response HTTP 200, success true, dan perubahan tersimpan
        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('users', [
            'id'            => $this->user->id,
            'name'          => 'Updated Name',
            'currency_code' => 'USD',
        ]);
    }

    /**
     * Test: User dapat memperbarui pengaturan preferensi aplikasinya.
     *
     * Memastikan endpoint settings menerima preferensi user seperti
     * notifikasi dan tema, lalu mengembalikan response sukses.
     */
    public function test_can_update_settings()
    {
        // ACT: Mengirimkan request PUT untuk memperbarui pengaturan aplikasi
        $response = $this->auth()->putJson('/api/profile/settings', [
            'notifications' => false,
            'theme'         => 'dark',
        ]);

        // ASSERT: Memastikan response HTTP 200 dan success bernilai true
        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }
}