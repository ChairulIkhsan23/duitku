<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

/**
 * CategoryService
 * 
 * Mengelola kategori transaksi pengguna termasuk inisialisasi kategori berdasarkan template,
 * membuat kategori custom, dan memperbarui kategori.
 * Service ini mendukung kategori default system dan kategori custom per pengguna.
 */
class CategoryService
{
    /**
     * initializeUserCategories
     * 
     * Menginisialisasi kategori pengguna berdasarkan template yang dipilih (standard, freelancer, mahasiswa).
     * Mencegah duplikasi kategori dengan mengecek kategori yang sudah ada.
     * 
     * @param User $user Pengguna yang akan mendapat kategori inisial
     * @param string $template Template kategori (default: 'standard')
     * @return void
     */
    public function initializeUserCategories(User $user, string $template = 'standard'): void
    {
        // Ambil template kategori yang sesuai
        $categories = $this->resolveTemplate($template);

        // Ambil kategori yang sudah ada untuk pengguna ini
        $existing = Category::where('user_id', $user->id)
            ->get()
            ->map(fn ($c) => $this->makeKey($c->name, $c->type))
            ->flip();

        $insert = [];

        // Iterasi template kategori
        foreach ($categories as $category) {
            // Buat unique key untuk mengecek duplikasi
            $key = $this->makeKey($category['name'], $category['type']);

            // Skip jika kategori sudah ada
            if (isset($existing[$key])) {
                continue;
            }

            // Siapkan data untuk insert
            $insert[] = [
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'is_default' => false,
                ...$category,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert semua kategori baru sekaligus
        if (!empty($insert)) {
            Category::insert($insert);
        }
    }

    /**
     * getUserCategories
     * 
     * Mengambil semua kategori yang tersedia untuk pengguna (kategori default + kategori custom).
     * Bisa di-filter berdasarkan tipe (income/expense).
     * 
     * @param User $user Pengguna yang kategorinya diambil
     * @param string|null $type Filter tipe kategori (income/expense), null untuk semua
     * @return Collection Collection kategori yang terfilter dan diurutkan
     */
    public function getUserCategories(User $user, ?string $type = null): Collection
    {
        return Category::query()
            ->where(function ($q) use ($user) {
                // Kategori default atau milik pengguna
                $q->where('is_default', true)
                ->orWhere('user_id', $user->id);
            })
            // Filter berdasarkan tipe jika diberikan
            ->when($type, fn ($q) => $q->where('type', $type))
            // Urutkan berdasarkan tipe dulu, kemudian nama
            ->orderBy('type')
            ->orderBy('name')
            ->get();
    }

    /**
     * createCategory
     * 
     * Membuat kategori custom baru untuk pengguna.
     * Mencegah duplikasi kategori dengan nama dan tipe yang sama.
     * 
     * @param User $user Pengguna yang akan membuat kategori
     * @param array $data Data kategori (name, type, icon, color, dll)
     * @return Category Kategori yang baru dibuat
     * @throws DomainException Jika kategori dengan nama dan tipe yang sama sudah ada
     */
    public function createCategory(User $user, array $data): Category
    {
        // Cek apakah kategori dengan nama dan tipe yang sama sudah ada
        if ($this->categoryExists($user, $data['name'], $data['type'])) {
            throw new \DomainException('Category already exists');
        }

        // Buat kategori baru
        return Category::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'is_default' => false,
            ...$data,
        ]);
    }

    /**
     * categoryExists
     * 
     * Memeriksa apakah kategori dengan nama dan tipe tertentu sudah ada untuk pengguna.
     * 
     * @param User $user Pengguna yang dicek kategorinya
     * @param string $name Nama kategori
     * @param string $type Tipe kategori (income/expense)
     * @return bool True jika kategori sudah ada
     */
    private function categoryExists(User $user, string $name, string $type): bool
    {
        return Category::where('user_id', $user->id)
            ->where('name', $name)
            ->where('type', $type)
            ->exists();
    }

    /**
     * updateCategory
     * 
     * Memperbarui kategori dengan data baru.
     * Kategori default system tidak dapat diupdate.
     * Mencegah duplikasi nama kategori dengan tipe yang sama.
     * 
     * @param Category $category Kategori yang akan diupdate
     * @param array $data Data baru untuk kategori
     * @return Category Kategori yang sudah diupdate
     * @throws Exception Jika kategori adalah default atau ada duplikasi
     */
    public function updateCategory(Category $category, array $data): Category
    {
        // Kategori default tidak dapat diupdate
        if ($category->is_default) {
            abort(403, 'Default category cannot be updated');
        }

        // Jika ada perubahan nama, cek duplikasi
        if (isset($data['name'])) {
            $exists = Category::where('user_id', $category->user_id)
                ->where('type', $category->type)
                ->where('name', $data['name'])
                ->where('id', '!=', $category->id)
                ->exists();

            if ($exists) {
                abort(409, 'Category name already exists');
            }
        }

        // Update kategori
        $category->update($data);

        // Return kategori terbaru dari database
        return $category->fresh();
    }

    /**
     * makeKey
     * 
     * Membuat unique key untuk kategori berdasarkan nama dan tipe.
     * Digunakan untuk pengecekan duplikasi dan perbandingan kategori.
     * 
     * @param string $name Nama kategori
     * @param string $type Tipe kategori
     * @return string Unique key dalam format "name|type"
     */
    private function makeKey(string $name, string $type): string
    {
        return $name . '|' . $type;
    }

    /**
     * resolveTemplate
     * 
     * Menolak template kategori yang sesuai berdasarkan nama template.
     * Template tersedia: standard, freelancer, mahasiswa.
     * 
     * @param string $template Nama template
     * @return array Array kategori dari template yang dipilih
     */
    private function resolveTemplate(string $template): array
    {
        return match ($template) {
            'freelancer' => $this->freelancerCategories(),
            'mahasiswa' => $this->mahasiswaCategories(),
            default => $this->defaultCategories(),
        };
    }

    /**
     * defaultCategories
     * 
     * Template kategori standard dengan kategori income dan expense umum.
     * Digunakan untuk mayoritas pengguna.
     * 
     * @return array Array berisi kategori default
     */
    private function defaultCategories(): array
    {
        return [
            // Kategori Pemasukan
            ['name' => 'Gaji Pokok', 'type' => 'income', 'icon' => 'FaWallet', 'color' => '#4CAF50', 'budget_default' => null],
            ['name' => 'Freelance', 'type' => 'income', 'icon' => 'FaLaptopCode', 'color' => '#2196F3', 'budget_default' => null],
            ['name' => 'Investasi', 'type' => 'income', 'icon' => 'FaChartLine', 'color' => '#9C27B0', 'budget_default' => null],
            ['name' => 'Bisnis', 'type' => 'income', 'icon' => 'FaStore', 'color' => '#FF9800', 'budget_default' => null],
            ['name' => 'Lain-lain', 'type' => 'income', 'icon' => 'FaEllipsisH', 'color' => '#9E9E9E', 'budget_default' => null],

            // Kategori Pengeluaran
            ['name' => 'Makan & Minum', 'type' => 'expense', 'icon' => 'FaUtensils', 'color' => '#FF5722', 'budget_default' => 1500000],
            ['name' => 'Transportasi', 'type' => 'expense', 'icon' => 'FaCar', 'color' => '#2196F3', 'budget_default' => 500000],
            ['name' => 'Hunian', 'type' => 'expense', 'icon' => 'FaHome', 'color' => '#795548', 'budget_default' => 2000000],
            ['name' => 'Belanja', 'type' => 'expense', 'icon' => 'FaShoppingCart', 'color' => '#FF9800', 'budget_default' => 800000],
            ['name' => 'Kesehatan', 'type' => 'expense', 'icon' => 'FaHeartbeat', 'color' => '#E91E63', 'budget_default' => 300000],
            ['name' => 'Hiburan', 'type' => 'expense', 'icon' => 'FaFilm', 'color' => '#9C27B0', 'budget_default' => 500000],
            ['name' => 'Pendidikan', 'type' => 'expense', 'icon' => 'FaGraduationCap', 'color' => '#00BCD4', 'budget_default' => 1000000],
            ['name' => 'Tabungan', 'type' => 'expense', 'icon' => 'FaPiggyBank', 'color' => '#4CAF50', 'budget_default' => 1000000],
            ['name' => 'Lain-lain', 'type' => 'expense', 'icon' => 'FaEllipsisH', 'color' => '#9E9E9E', 'budget_default' => 100000],
        ];
    }

    /**
     * freelancerCategories
     * 
     * Template kategori untuk pengguna freelancer dengan fokus pada proyek dan tools.
     * 
     * @return array Array berisi kategori untuk freelancer
     */
    private function freelancerCategories(): array
    {
        return [
            // Kategori Pemasukan Freelancer
            ['name' => 'Proyek Design', 'type' => 'income', 'icon' => 'FaPaintBrush', 'color' => '#9C27B0', 'budget_default' => null],
            ['name' => 'Proyek Coding', 'type' => 'income', 'icon' => 'FaCode', 'color' => '#2196F3', 'budget_default' => null],
            ['name' => 'Proyek Writing', 'type' => 'income', 'icon' => 'FaPenFancy', 'color' => '#4CAF50', 'budget_default' => null],
            // Kategori Pengeluaran Freelancer
            ['name' => 'Tools Subscription', 'type' => 'expense', 'icon' => 'FaCloud', 'color' => '#FF9800', 'budget_default' => 500000],
        ];
    }

    /**
     * mahasiswaCategories
     * 
     * Template kategori untuk pengguna mahasiswa dengan fokus pada pendidikan dan biaya hidup.
     * 
     * @return array Array berisi kategori untuk mahasiswa
     */
    private function mahasiswaCategories(): array
    {
        return [
            // Kategori Pemasukan Mahasiswa
            ['name' => 'Uang Saku', 'type' => 'income', 'icon' => 'FaMoneyBill', 'color' => '#4CAF50', 'budget_default' => null],
            // Kategori Pengeluaran Mahasiswa
            ['name' => 'Buku', 'type' => 'expense', 'icon' => 'FaBook', 'color' => '#2196F3', 'budget_default' => 300000],
            ['name' => 'SPP', 'type' => 'expense', 'icon' => 'FaUniversity', 'color' => '#F44336', 'budget_default' => 1000000],
            ['name' => 'Kos', 'type' => 'expense', 'icon' => 'FaBed', 'color' => '#795548', 'budget_default' => 800000],
        ];
    }
}