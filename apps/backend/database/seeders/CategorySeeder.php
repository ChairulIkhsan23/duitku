<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Income Categories (10)
            ['id' => Str::uuid(), 'name' => 'Gaji Pokok', 'type' => 'income', 'icon' => 'FaWallet', 'color' => '#4CAF50', 'budget_default' => null, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Pendapatan Freelance', 'type' => 'income', 'icon' => 'FaLaptopCode', 'color' => '#2196F3', 'budget_default' => null, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Investasi', 'type' => 'income', 'icon' => 'FaChartLine', 'color' => '#9C27B0', 'budget_default' => null, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Bisnis Sampingan', 'type' => 'income', 'icon' => 'FaStore', 'color' => '#FF9800', 'budget_default' => null, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Cashback/Reward', 'type' => 'income', 'icon' => 'FaGift', 'color' => '#E91E63', 'budget_default' => null, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Hadiah/Donasi', 'type' => 'income', 'icon' => 'FaGift', 'color' => '#FF5722', 'budget_default' => null, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Hasil Jual Barang', 'type' => 'income', 'icon' => 'FaTag', 'color' => '#795548', 'budget_default' => null, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Refund', 'type' => 'income', 'icon' => 'FaUndo', 'color' => '#607D8B', 'budget_default' => null, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Uang Saku', 'type' => 'income', 'icon' => 'FaMoneyBillWave', 'color' => '#8BC34A', 'budget_default' => null, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Lain-lain', 'type' => 'income', 'icon' => 'FaEllipsisH', 'color' => '#9E9E9E', 'budget_default' => null, 'is_default' => true, 'user_id' => null],
            
            // Expense Categories (20)
            ['id' => Str::uuid(), 'name' => 'Makan & Minum', 'type' => 'expense', 'icon' => 'FaUtensils', 'color' => '#FF5722', 'budget_default' => 1500000, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Transportasi', 'type' => 'expense', 'icon' => 'FaCar', 'color' => '#2196F3', 'budget_default' => 500000, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Hunian', 'type' => 'expense', 'icon' => 'FaHome', 'color' => '#795548', 'budget_default' => 2000000, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Belanja Bulanan', 'type' => 'expense', 'icon' => 'FaShoppingCart', 'color' => '#FF9800', 'budget_default' => 800000, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Kesehatan', 'type' => 'expense', 'icon' => 'FaHeartbeat', 'color' => '#E91E63', 'budget_default' => 300000, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Hiburan', 'type' => 'expense', 'icon' => 'FaFilm', 'color' => '#9C27B0', 'budget_default' => 500000, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Pendidikan', 'type' => 'expense', 'icon' => 'FaGraduationCap', 'color' => '#00BCD4', 'budget_default' => 1000000, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Pakaian', 'type' => 'expense', 'icon' => 'FaTshirt', 'color' => '#3F51B5', 'budget_default' => 400000, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Tabungan/Investasi', 'type' => 'expense', 'icon' => 'FaPiggyBank', 'color' => '#4CAF50', 'budget_default' => 1000000, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Sosial', 'type' => 'expense', 'icon' => 'FaUsers', 'color' => '#FF4081', 'budget_default' => 200000, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Utang/Cicilan', 'type' => 'expense', 'icon' => 'FaCreditCard', 'color' => '#F44336', 'budget_default' => 1000000, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Pajak', 'type' => 'expense', 'icon' => 'FaFileInvoiceDollar', 'color' => '#607D8B', 'budget_default' => 500000, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Perawatan Diri', 'type' => 'expense', 'icon' => 'FaSpa', 'color' => '#FF69B4', 'budget_default' => 200000, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Hewan Peliharaan', 'type' => 'expense', 'icon' => 'FaDog', 'color' => '#8D6E63', 'budget_default' => 300000, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Gadget & Elektronik', 'type' => 'expense', 'icon' => 'FaMobileAlt', 'color' => '#546E7A', 'budget_default' => 500000, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Travel & Liburan', 'type' => 'expense', 'icon' => 'FaPlane', 'color' => '#00BCD4', 'budget_default' => 1000000, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Keperluan Kantor', 'type' => 'expense', 'icon' => 'FaBriefcase', 'color' => '#78909C', 'budget_default' => 100000, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Iuran/Donasi', 'type' => 'expense', 'icon' => 'FaHandHoldingHeart', 'color' => '#FF6D00', 'budget_default' => 100000, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Dapur', 'type' => 'expense', 'icon' => 'FaKitchenSet', 'color' => '#8BC34A', 'budget_default' => 300000, 'is_default' => true, 'user_id' => null],
            ['id' => Str::uuid(), 'name' => 'Lain-lain', 'type' => 'expense', 'icon' => 'FaEllipsisH', 'color' => '#9E9E9E', 'budget_default' => 100000, 'is_default' => true, 'user_id' => null],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('✅ ' . Category::count() . ' kategori berhasil dibuat.');
    }
}
