<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

/**
 * Feature Test: Authentication
 *
 * Purpose: Menguji seluruh alur autentikasi pengguna pada sistem,
 *          mulai dari registrasi, login, logout, hingga pengambilan profil.
 *
 * Coverage:
 * - Registrasi user baru dengan data valid
 * - Login user menggunakan kredensial yang benar
 * - Logout user dan invalidasi token
 * - Pengambilan data profil user yang sedang login
 *
 * @group auth
 */
class AuthTest extends TestCase
{

    /**
     * Test: User dapat melakukan registrasi dengan data yang valid.
     *
     * Memastikan bahwa endpoint registrasi menerima data yang benar,
     * mengembalikan HTTP 201 Created, dan menyimpan user ke database.
     */
    public function test_user_can_register()
    {
        // ARRANGE: Menyiapkan payload registrasi dengan data user yang valid
        $payload = [
            'name'                  => 'Test User',
            'email'                 => 'test@test.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ];

        // ACT: Mengirimkan request POST ke endpoint registrasi
        $response = $this->postJson('/api/register', $payload);

        // ASSERT: Memastikan response HTTP 201 dan data tersimpan di database
        $response->assertCreated();

        $this->assertDatabaseHas('users', [
            'email' => 'test@test.com',
        ]);
    }

    /**
     * Test: User dapat melakukan login dengan kredensial yang benar.
     *
     * Memastikan bahwa endpoint login mengembalikan token autentikasi
     * beserta data user dalam struktur JSON yang sesuai.
     */
    public function test_user_can_login()
    {
        // ARRANGE: Membuat user di database dengan password yang sudah di-hash
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        // ACT: Mengirimkan request POST ke endpoint login dengan kredensial valid
        $response = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        // ASSERT: Memastikan response HTTP 200 dan struktur JSON mengandung token & data user
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token',
                    'user',
                ]
            ]);
    }

    /**
     * Test: User yang sedang login dapat melakukan logout.
     *
     * Memastikan bahwa setelah logout, token personal access
     * dihapus dari database sehingga tidak dapat digunakan kembali.
     */
    public function test_user_can_logout()
    {
        // ARRANGE: Membuat user terautentikasi menggunakan helper actingAsUser()
        $this->actingAsUser();

        // ACT: Mengirimkan request POST ke endpoint logout dengan token aktif
        $response = $this->auth()->postJson('/api/logout');

        // ASSERT: Memastikan response HTTP 200 dan token dihapus dari database
        $response->assertOk();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $this->authUser->id,
        ]);
    }

    /**
     * Test: User yang sedang login dapat mengambil data profilnya sendiri.
     *
     * Memastikan bahwa endpoint /api/user mengembalikan informasi
     * dasar user yang sedang terautentikasi dalam format JSON yang benar.
     */
    public function test_can_get_user_profile()
    {
        // ARRANGE: Membuat user terautentikasi menggunakan helper actingAsUser()
        $this->actingAsUser();

        // ACT: Mengirimkan request GET ke endpoint profil user
        $response = $this->auth()->getJson('/api/user');

        // ASSERT: Memastikan response HTTP 200 dan struktur JSON profil user sesuai
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at'
                ]
            ]);
    }
}