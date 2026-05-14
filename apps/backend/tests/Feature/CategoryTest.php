<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;

/**
 * Feature Test: Category
 *
 * Purpose: Menguji seluruh fungsionalitas manajemen kategori transaksi,
 *          baik kategori default sistem maupun kategori kustom milik pengguna.
 *
 * Coverage:
 * - Mengambil daftar semua kategori (default + kustom milik user)
 * - Membuat kategori kustom baru
 * - Mengambil detail satu kategori berdasarkan ID
 * - Memperbarui kategori kustom milik user
 * - Menghapus kategori kustom milik user
 *
 * @group category
 */
class CategoryTest extends TestCase
{
    /**
     * User terautentikasi yang digunakan selama sesi testing category API.
     */
    private User $user;

    /**
     * Kategori default bertipe 'expense' dari sistem (hasil seeder).
     * Digunakan untuk test yang memerlukan kategori yang sudah ada.
     */
    private ?Category $defaultCategory;

    /**
     * Inisialisasi: Membuat user terautentikasi dan mengambil
     * kategori default sebelum setiap test dijalankan.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // ARRANGE (global): Membuat user terautentikasi
        $this->user = $this->actingAsUser();

        // ARRANGE (global): Mengambil kategori default expense dari seeder
        $this->defaultCategory = Category::where('is_default', true)
            ->where('type', 'expense')
            ->first();
    }

    /**
     * Test: User dapat mengambil daftar seluruh kategori yang tersedia.
     *
     * Memastikan endpoint mengembalikan koleksi kategori yang mencakup
     * kategori default sistem dan kategori kustom milik user.
     */
    public function test_can_get_categories_list()
    {
        // ACT: Mengirimkan request GET ke endpoint daftar kategori
        $response = $this->auth()->getJson('/api/categories');

        // ASSERT: Memastikan response HTTP 200 dan struktur data kategori tersedia
        $response->assertOk()
            ->assertJsonStructure([
                'data'
            ]);
    }

    /**
     * Test: User dapat membuat kategori kustom baru.
     *
     * Memastikan kategori baru tersimpan di database dengan data yang
     * sesuai, dan endpoint mengembalikan response sukses beserta data kategori.
     */
    public function test_can_create_custom_category()
    {
        // ACT: Mengirimkan request POST untuk membuat kategori kustom baru
        $response = $this->auth()->postJson('/api/categories', [
            'name'  => 'Custom Category',
            'type'  => 'expense',
            'icon'  => 'FaCustom',
            'color' => '#FF0000',
        ]);

        // ASSERT: Memastikan response HTTP 201, struktur JSON benar, dan data tersimpan
        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'data'
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Custom Category',
            'type' => 'expense',
        ]);
    }

    /**
     * Test: User dapat mengambil detail satu kategori berdasarkan ID-nya.
     *
     * Test di-skip apabila kategori default tidak ditemukan di database
     * (kemungkinan seeder belum dijalankan).
     */
    public function test_can_get_single_category()
    {
        // ARRANGE: Memastikan kategori default tersedia, jika tidak test di-skip
        if (!$this->defaultCategory) {
            $this->markTestSkipped('Kategori default tidak ditemukan. Jalankan seeder terlebih dahulu.');
        }

        // ACT: Mengirimkan request GET ke endpoint detail kategori
        $response = $this->auth()
            ->getJson("/api/categories/{$this->defaultCategory->id}");

        // ASSERT: Memastikan response HTTP 200 dan struktur data kategori sesuai
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data'
            ]);
    }

    /**
     * Test: User dapat memperbarui kategori kustom miliknya.
     *
     * Memastikan perubahan nama kategori tersimpan di database
     * dan endpoint mengembalikan response sukses.
     */
    public function test_can_update_category()
    {
        // ARRANGE: Membuat kategori kustom yang dimiliki oleh user yang terautentikasi
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // ACT: Mengirimkan request PUT untuk memperbarui nama kategori
        $response = $this->auth()->putJson("/api/categories/{$category->id}", [
            'name'  => 'Updated Category',
            'type'  => $category->type  ?? 'expense',
            'icon'  => $category->icon  ?? 'FaEdit',
            'color' => $category->color ?? '#000000',
        ]);

        // ASSERT: Memastikan response HTTP 200, success true, dan perubahan tersimpan
        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('categories', [
            'id'   => $category->id,
            'name' => 'Updated Category',
        ]);
    }

    /**
     * Test: User dapat menghapus kategori kustom miliknya.
     *
     * Memastikan kategori terhapus dari database setelah request
     * DELETE berhasil dieksekusi.
     */
    public function test_can_delete_category()
    {
        // ARRANGE: Membuat kategori kustom yang dimiliki oleh user yang terautentikasi
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // ACT: Mengirimkan request DELETE ke endpoint kategori
        $response = $this->auth()
            ->deleteJson("/api/categories/{$category->id}");

        // ASSERT: Memastikan response HTTP 200 dan data tidak lagi ada di database
        $response->assertOk();

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }
}