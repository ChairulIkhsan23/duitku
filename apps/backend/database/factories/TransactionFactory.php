<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory untuk model Transaction
 *
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Default state untuk data Transaction.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /**
         * Menentukan tipe transaksi secara acak:
         * - income  : pemasukan
         * - expense : pengeluaran
         */
        $type = $this->faker->randomElement(['income', 'expense']);

        /**
         * Mengambil kategori sesuai tipe transaksi.
         * Prioritas: kategori default jika tersedia,
         * jika tidak ada maka dibuat baru.
         */
        $category = Category::where('type', $type)
            ->where('is_default', true)
            ->inRandomOrder()
            ->first();

        if (!$category) {
            $category = Category::factory()->create([
                'type' => $type,
            ]);
        }

        /**
         * Menentukan nominal transaksi berdasarkan tipe:
         * - income  : lebih besar
         * - expense : lebih kecil
         */
        $amount = $type === 'income'
            ? $this->faker->numberBetween(50000, 15000000)
            : $this->faker->numberBetween(5000, 3000000);

        /**
         * Daftar catatan transaksi (simulasi aktivitas umum)
         */
        $notes = [
            'Starbucks',
            'Makan siang',
            'Gojek',
            'Bensin',
            'Listrik',
            'Netflix',
            'Gaji bulanan',
            'Freelance project',
            'Belanja bulanan',
            'Kopi Kenangan',
            'MCD',
            'KFC',
            'Pulsa',
            'Parkir',
            'Tol'
        ];

        return [
            'id' => Str::uuid(),

            'user_id' => User::factory(),

            'category_id' => $category->id,

            'amount' => $amount,

            'type' => $type,

            /**
             * Tanggal transaksi (hingga 3 bulan terakhir)
             */
            'date' => $this->faker->dateTimeBetween('-3 months', 'now'),

            /**
             * Catatan transaksi
             */
            'note' => $this->faker->randomElement($notes),

            'photo_url' => null,

            'is_duplicate' => false,

            /**
             * Lokasi transaksi (simulasi kota)
             */
            'location_name' => $this->faker->city(),

            'metadata' => null,

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }

    /**
     * State: transaksi income
     */
    public function income(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'income',
            'amount' => $this->faker->numberBetween(50000, 15000000),
        ]);
    }

    /**
     * State: transaksi expense
     */
    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'expense',
            'amount' => $this->faker->numberBetween(5000, 3000000),
        ]);
    }

    /**
     * State: transaksi hari ini
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => now(),
        ]);
    }
}