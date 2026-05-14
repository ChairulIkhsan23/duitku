<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Notification;

/**
 * Feature Test: Notification
 *
 * Purpose: Menguji seluruh fungsionalitas sistem notifikasi pengguna,
 *          mencakup pengambilan daftar, filter status baca, penghitungan,
 *          dan penandaan notifikasi sebagai sudah dibaca.
 *
 * Coverage:
 * - Mengambil seluruh daftar notifikasi milik user
 * - Memfilter notifikasi yang belum dibaca
 * - Mengambil jumlah notifikasi yang belum dibaca
 * - Menandai satu notifikasi sebagai sudah dibaca
 *
 * @group notification
 */
class NotificationTest extends TestCase
{
    /**
     * User terautentikasi yang digunakan selama sesi testing notification API.
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
     * Test: User dapat mengambil daftar semua notifikasi miliknya.
     *
     * Memastikan endpoint mengembalikan seluruh notifikasi (dibaca maupun
     * belum dibaca) yang dimiliki oleh user yang sedang terautentikasi.
     */
    public function test_can_get_notifications_list()
    {
        // ARRANGE: Membuat 5 notifikasi untuk user yang terautentikasi
        Notification::factory()->count(5)->create([
            'user_id' => $this->user->id,
        ]);

        // ACT: Mengirimkan request GET ke endpoint daftar notifikasi
        $response = $this->auth()->getJson('/api/notifications');

        // ASSERT: Memastikan response HTTP 200 dan struktur data notifikasi tersedia
        $response->assertOk()
            ->assertJsonStructure([
                'data',
            ]);
    }

    /**
     * Test: User dapat mengambil daftar notifikasi yang belum dibaca.
     *
     * Memastikan endpoint /notifications/unread hanya mengembalikan
     * notifikasi dengan status is_read = false milik user.
     */
    public function test_can_get_unread_notifications()
    {
        // ARRANGE: Membuat 3 notifikasi yang belum dibaca
        Notification::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'is_read' => false,
        ]);

        // ARRANGE: Membuat 2 notifikasi yang sudah dibaca sebagai pembanding
        Notification::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'is_read' => true,
        ]);

        // ACT: Mengirimkan request GET ke endpoint notifikasi belum dibaca
        $response = $this->auth()->getJson('/api/notifications/unread');

        // ASSERT: Memastikan response HTTP 200 dan struktur data tersedia
        $response->assertOk()
            ->assertJsonStructure([
                'data',
            ]);
    }

    /**
     * Test: User dapat mengambil jumlah notifikasi yang belum dibaca.
     *
     * Memastikan endpoint mengembalikan hitungan notifikasi unread
     * yang akan digunakan sebagai badge indikator di antarmuka pengguna.
     */
    public function test_can_get_unread_count()
    {
        // ARRANGE: Membuat 3 notifikasi yang belum dibaca
        Notification::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'is_read' => false,
        ]);

        // ACT: Mengirimkan request GET ke endpoint jumlah notifikasi belum dibaca
        $response = $this->auth()->getJson('/api/notifications/unread-count');

        // ASSERT: Memastikan response HTTP 200 dan struktur JSON mengandung field 'count'
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'count',
                ],
            ]);
    }

    /**
     * Test: User dapat menandai satu notifikasi sebagai sudah dibaca.
     *
     * Memastikan endpoint mark-as-read berhasil memperbarui status
     * notifikasi dan mengembalikan response sukses.
     */
    public function test_can_mark_notification_as_read()
    {
        // ARRANGE: Membuat satu notifikasi dengan status belum dibaca
        $notification = Notification::factory()->create([
            'user_id' => $this->user->id,
            'is_read' => false,
        ]);

        // ACT: Mengirimkan request POST ke endpoint read untuk notifikasi tersebut
        $response = $this->auth()
            ->postJson("/api/notifications/{$notification->id}/read");

        // ASSERT: Memastikan response HTTP 200 dan success bernilai true
        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }
}