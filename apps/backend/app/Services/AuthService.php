<?php

namespace App\Services;

use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken; 
use Illuminate\Support\Facades\DB;

/**
 * AuthService
 * 
 * Mengelola proses autentikasi pengguna termasuk registrasi, login, logout,
 * dan pembaruan profil. Service ini menangani validasi email, enkripsi password,
 * dan pembuatan token autentikasi.
 */
class AuthService
{
    /**
     * register
     * 
     * Mendaftarkan pengguna baru dengan data yang diberikan.
     * Melakukan validasi email (unik dan format valid), enkripsi password,
     * dan pembuatan token autentikasi untuk sesi pertama.
     * 
     * @param array $data Data registrasi (name, email, password, currency_code, dll)
     * @return array Array berisi user dan token autentikasi
     */
    public function register(array $data): array
    {
        // Normalize email ke lowercase dan hapus whitespace
        $email = strtolower(trim($data['email'] ?? ''));

        // Validasi email: cek jika kosong atau sudah terdaftar
        if ($email === '' || User::where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => ['Email already in use or invalid'],
            ]);
        }

        // Set default template
        $template = $data['onboarding_template'] ?? 'standard';

        // Buat pengguna baru dengan data pemberian nilai default jika tidak ada
        $user = User::create([
            'id' => Str::uuid(),
            'name' => $data['name'],
            'email' => $email,
            'password' => Hash::make($data['password']),
            'currency_code' => $data['currency_code'] ?? 'IDR',
            'onboarding_template' => $template,
            'initial_balance' => $data['initial_balance'] ?? 0,
            'streak_days' => 0, 
            'is_premium' => false, 
        ]);

        // Buat kategori default berdasarkan template
        $this->createDefaultCategories($user, $template);

        // Buat token autentikasi untuk sesi pertama
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * createDefaultCategories
     * 
     * Membuat kategori default berdasarkan template onboarding yang dipilih.
     * 
     * @param User $user Pengguna yang akan dibuatkan kategorinya
     * @param string $template Template onboarding (standard, freelancer, mahasiswa)
     * @return void
     */
    protected function createDefaultCategories(User $user, string $template): void
    {
        $categories = $this->getCategoriesByTemplate($template);
        
        foreach ($categories as $category) {
            Category::create([
                'user_id' => $user->id,
                'name' => $category['name'],
                'type' => $category['type'],
                'icon' => $category['icon'],
                'color' => $category['color'],
                'is_default' => $category['is_default'] ?? false,
                'budget_default' => $category['budget_default'] ?? null,
            ]);
        }
    }

    /**
     * getCategoriesByTemplate
     * 
     * Mendapatkan daftar kategori berdasarkan template yang dipilih.
     * 
     * @param string $template Template onboarding (standard, freelancer, mahasiswa)
     * @return array Daftar kategori yang akan dibuat
     */
    protected function getCategoriesByTemplate(string $template): array
    {
        $templates = [
            'standard' => [
                // Income categories
                ['name' => 'Gaji', 'type' => 'income', 'icon' => 'FaMoneyBill', 'color' => '#4CAF50', 'is_default' => false],
                ['name' => 'Bonus', 'type' => 'income', 'icon' => 'FaGift', 'color' => '#FFC107', 'is_default' => false],
                // Expense categories
                ['name' => 'Makan & Minum', 'type' => 'expense', 'icon' => 'FaUtensils', 'color' => '#FF9800', 'is_default' => false],
                ['name' => 'Transportasi', 'type' => 'expense', 'icon' => 'FaCar', 'color' => '#2196F3', 'is_default' => false],
                ['name' => 'Belanja', 'type' => 'expense', 'icon' => 'FaShoppingBag', 'color' => '#E91E63', 'is_default' => false],
                ['name' => 'Tagihan', 'type' => 'expense', 'icon' => 'FaFileInvoice', 'color' => '#F44336', 'is_default' => false],
                ['name' => 'Hiburan', 'type' => 'expense', 'icon' => 'FaTicketAlt', 'color' => '#9C27B0', 'is_default' => false],
                ['name' => 'Kesehatan', 'type' => 'expense', 'icon' => 'FaHeartbeat', 'color' => '#00BCD4', 'is_default' => false],
            ],
            'mahasiswa' => [
                // Income categories
                ['name' => 'Uang Saku', 'type' => 'income', 'icon' => 'FaMoneyBill', 'color' => '#4CAF50', 'is_default' => false],
                ['name' => 'Part Time', 'type' => 'income', 'icon' => 'FaBriefcase', 'color' => '#2196F3', 'is_default' => false],
                ['name' => 'Beasiswa', 'type' => 'income', 'icon' => 'FaGraduationCap', 'color' => '#9C27B0', 'is_default' => false],
                // Expense categories
                ['name' => 'Makan', 'type' => 'expense', 'icon' => 'FaUtensils', 'color' => '#FF9800', 'is_default' => false],
                ['name' => 'Transport', 'type' => 'expense', 'icon' => 'FaBus', 'color' => '#2196F3', 'is_default' => false],
                ['name' => 'Buku & Alat Tulis', 'type' => 'expense', 'icon' => 'FaBook', 'color' => '#9C27B0', 'is_default' => false],
                ['name' => 'Kos', 'type' => 'expense', 'icon' => 'FaHome', 'color' => '#795548', 'is_default' => false],
                ['name' => 'Hiburan', 'type' => 'expense', 'icon' => 'FaGamepad', 'color' => '#E91E63', 'is_default' => false],
                ['name' => 'Tabungan', 'type' => 'expense', 'icon' => 'FaPiggyBank', 'color' => '#FFC107', 'is_default' => false],
            ],
            'freelancer' => [
                // Income categories
                ['name' => 'Pendapatan Proyek', 'type' => 'income', 'icon' => 'FaBriefcase', 'color' => '#4CAF50', 'is_default' => false],
                ['name' => 'Passive Income', 'type' => 'income', 'icon' => 'FaChartLine', 'color' => '#8BC34A', 'is_default' => false],
                ['name' => 'Konsultasi', 'type' => 'income', 'icon' => 'FaUsers', 'color' => '#00BCD4', 'is_default' => false],
                // Expense categories
                ['name' => 'Operasional', 'type' => 'expense', 'icon' => 'FaLaptop', 'color' => '#FF9800', 'is_default' => false],
                ['name' => 'Pajak', 'type' => 'expense', 'icon' => 'FaFileInvoice', 'color' => '#F44336', 'is_default' => false],
                ['name' => 'Marketing', 'type' => 'expense', 'icon' => 'FaBullhorn', 'color' => '#9C27B0', 'is_default' => false],
                ['name' => 'Software & Tools', 'type' => 'expense', 'icon' => 'FaCode', 'color' => '#2196F3', 'is_default' => false],
                ['name' => 'Pengembangan Skill', 'type' => 'expense', 'icon' => 'FaGraduationCap', 'color' => '#673AB7', 'is_default' => false],
            ],
        ];

        // Default categories untuk semua template
        $defaultCategories = [
            ['name' => 'Pendapatan Lain', 'type' => 'income', 'icon' => 'FaPlusCircle', 'color' => '#8BC34A', 'is_default' => true],
            ['name' => 'Pengeluaran Lain', 'type' => 'expense', 'icon' => 'FaMinusCircle', 'color' => '#FF5722', 'is_default' => true],
        ];

        // Ambil template categories atau fallback ke standard
        $templateCategories = $templates[$template] ?? $templates['standard'];
        
        // Merge dengan default categories
        return array_merge($templateCategories, $defaultCategories);
    }

    /**
     * createInitialBalanceTransaction
     * 
     * Membuat transaksi untuk saldo awal pengguna.
     * 
     * @param User $user Pengguna yang memiliki saldo awal
     * @param float $amount Jumlah saldo awal
     * @return void
     */
    protected function createInitialBalanceTransaction(User $user, float $amount): void
    {
        // CEK: Apakah sudah ada transaksi saldo awal?
        $existingTransaction = DB::table('transactions')
            ->where('user_id', $user->id)
            ->where('note', 'Saldo awal')
            ->where('type', 'income')
            ->first();
        
        // Jika sudah ada, jangan buat lagi
        if ($existingTransaction) {
            return;
        }
        
        // Cari atau buat kategori Saldo Awal
        $category = Category::where('user_id', $user->id)
            ->where('name', 'Saldo Awal')
            ->where('type', 'income')
            ->first();
        
        if (!$category) {
            $category = Category::create([
                'user_id' => $user->id,
                'name' => 'Saldo Awal',
                'type' => 'income',
                'icon' => 'FaWallet',
                'color' => '#4CAF50',
                'is_default' => true,
            ]);
        }

        // Buat transaksi
        $user->transactions()->create([
            'id' => \Illuminate\Support\Str::uuid(),
            'category_id' => $category->id,
            'amount' => $amount,
            'type' => 'income',
            'date' => now(),
            'note' => 'Saldo awal',
        ]);
    }

    /**
     * login
     * 
     * Melakukan login pengguna dengan email dan password.
     * Memverifikasi kredensial pengguna dan membuat token autentikasi jika valid.
     * 
     * @param string $email Email pengguna (akan diubah menjadi lowercase)
     * @param string $password Password pengguna yang belum di-hash
     * @return array Array berisi user dan token autentikasi
     * @throws ValidationException Jika email atau password tidak sesuai
     */
    public function login(string $email, string $password): array
    {
        // Normalize email ke lowercase dan hapus whitespace
        $email = strtolower(trim($email));

        // Cari pengguna berdasarkan email
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * logout
     * 
     * Menghapus token autentikasi pengguna yang sedang aktif.
     * Ini akan menghentikan sesi pengguna dan memaksa login kembali di kali berikutnya.
     * 
     * @param User $user Pengguna yang akan di-logout
     * @return void
     */
    public function logout($user): void
    {
        // Hapus token akses yang sedang digunakan
        $token = $user->currentAccessToken();
        
        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }
    }

    /**
     * updateProfile
     * 
     * Memperbarui profil pengguna dengan data yang diizinkan.
     * Hanya field tertentu yang dapat diupdate (name, email, currency_code, dll).
     * Melakukan validasi untuk email yang unique dan password akan di-hash jika diubah.
     * 
     * @param User $user Pengguna yang akan diupdate
     * @param array $data Data profil yang ingin diupdate
     * @return User Pengguna yang sudah diupdate
     * @throws ValidationException Jika email sudah digunakan pengguna lain
     */
    public function updateProfile($user, array $data): User
    {
        // Hanya izinkan field tertentu untuk diupdate (whitelist)
        $allowed = collect($data)->only([
            'name',
            'email',
            'currency_code',
            'avatar',
            'settings',
            'password',
            'onboarding_template'
        ])->toArray();

        // Validasi onboarding_template jika ada
        if (isset($allowed['onboarding_template'])) {
            $validTemplates = ['standard', 'freelancer', 'mahasiswa'];
            if (!in_array($allowed['onboarding_template'], $validTemplates)) {
                throw ValidationException::withMessages([
                    'onboarding_template' => ['Template harus standard, freelancer, atau mahasiswa'],
                ]);
            }
        }

        // Validasi dan normalize email jika ada
        if (isset($allowed['email'])) {
            $allowed['email'] = strtolower(trim($allowed['email']));

            // Cek apakah email sudah digunakan pengguna lain
            if (
                User::where('email', $allowed['email'])
                    ->where('id', '!=', $user->id)
                    ->exists()
            ) {
                throw ValidationException::withMessages([
                    'email' => ['Email already in use'],
                ]);
            }
        }

        // Hash password jika ada dan tidak kosong
        if (isset($allowed['password']) && trim($allowed['password']) !== '') {
            $allowed['password'] = Hash::make($allowed['password']);
        } else {
            // Hapus password dari array jika kosong
            unset($allowed['password']);
        }

        // Merge settings dengan existing settings jika ada
        if (isset($allowed['settings'])) {
            $allowed['settings'] = array_merge(
                is_array($user->settings) ? $user->settings : [],
                is_array($allowed['settings']) ? $allowed['settings'] : []
            );
        }

        // Simpan perubahan dan return user terbaru
        $user->update($allowed);

        return $user->fresh();
    }
}