<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory untuk model Category
 *
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Default state untuk data Category.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /**
         * Menentukan tipe kategori secara acak:
         * - income  : pemasukan
         * - expense : pengeluaran
         */
        $types = ['income', 'expense'];
        $type = $this->faker->randomElement($types);

        return [
            'id' => Str::uuid(),

            'name' => $this->faker->word(),

            'type' => $type,

            /**
             * Icon kategori (simulasi UI icon)
             */
            'icon' => $this->faker->randomElement([
                'FaMoneyBill',
                'FaUtensils',
                'FaCar',
                'FaHome',
                'FaShoppingCart'
            ]),

            /**
             * Warna kategori dalam format hex
             */
            'color' => $this->faker->hexColor(),

            /**
             * Default budget hanya berlaku untuk kategori expense
             * untuk income akan bernilai null
             */
            'budget_default' => $type === 'expense'
                ? $this->faker->numberBetween(500000, 5000000)
                : null,

            /**
             * Menandakan apakah kategori ini bawaan sistem
             */
            'is_default' => false,

            'user_id' => User::factory(),

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}