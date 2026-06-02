<?php

/**
 * Keyword Mapping Data
 *
 * Digunakan untuk mencocokkan transaksi berdasarkan keyword
 * ke kategori tertentu secara otomatis oleh sistem.
 *
 * STRUKTUR:
 * - keywords         : array kata kunci pemicu kategori
 * - category_name    : nama kategori tujuan mapping
 * - confidence       : tingkat kepercayaan (0 - 1)
 * - is_active        : status aktif/nonaktif mapping
 * - created_by       : null (default system mapping)
 *
 * NOTE:
 * - Tidak menggunakan id (UUID akan digenerate oleh model/seeder)
 * - keywords akan di-encode di seeder jika dibutuhkan JSON
 * - File ini hanya sebagai SOURCE OF TRUTH (data mentah)
 */

return [

    // ======================================================
    // FOOD & BEVERAGE
    // ======================================================
    [
        'keywords' => [
            'starbucks', 'kopi kenangan', 'fore', 'jco', 'chatime',
            'mcd', 'mcdonald', 'kfc', 'burger king', 'pizza hut',
            'domino pizza', 'hokben', 'restoran', 'warteg', 'warung',
            'cafe', 'coffee shop', 'eatery', 'food court',
            'grab food', 'gofood', 'shopee food', 'kuliner',
            'makan siang', 'makan malam', 'sarapan', 'bubur', 'bakso',
            'mi ayam', 'nasi goreng', 'soto', 'rawon', 'gado-gado',
            'pecel', 'sate', 'rendang', 'ayam goreng', 'ikan bakar',
            'seafood', 'sushi', 'ramen', 'hotpot', 'buffet',
            'catering', 'bento', 'snack', 'cemilan', 'jajan'
        ],
        'category_name' => 'Makan & Minum',
        'confidence' => 0.90,
        'is_active' => true,
        'created_by' => null,
    ],

    // ======================================================
    // TRANSPORT
    // ======================================================
    [
        'keywords' => [
            'grab', 'gojek', 'gocar', 'goride', 'taxi', 'blue bird',
            'kereta', 'kai', 'transjakarta', 'bus', 'angkot',
            'bensin', 'pertalite', 'pertamax', 'solar',
            'tol', 'e-toll', 'e money', 'flazz', 'brizzi',
            'parkir', 'motor', 'mobil', 'bengkel', 'service',
            'oli', 'spbu', 'shell', 'vivo', 'bp fuel',
            'mrt', 'lrt', 'krl', 'commuter line', 'bis',
            'travel', 'shuttle', 'bandara', 'pesawat', 'lion air',
            'garuda', 'batik air', 'citilink', 'scooter', 'sewa motor'
        ],
        'category_name' => 'Transportasi',
        'confidence' => 0.90,
        'is_active' => true,
        'created_by' => null,
    ],

    // ======================================================
    // HOUSING
    // ======================================================
    [
        'keywords' => [
            'listrik', 'pln', 'air', 'pdam', 'pam',
            'internet', 'wifi', 'indihome', 'first media', 'biznet',
            'kontrakan', 'kos', 'sewa rumah', 'apartemen',
            'kpr', 'cicilan rumah', 'iuran lingkungan',
            'ipkl', 'sampah', 'keamanan', 'lingkungan',
            'pbb', 'pajak bumi', 'ibt', 'gas', 'tabung gas',
            'elpiji', 'perabotan', 'furniture', 'kulkas', 'ac'
        ],
        'category_name' => 'Hunian',
        'confidence' => 0.90,
        'is_active' => true,
        'created_by' => null,
    ],

    // ======================================================
    // ENTERTAINMENT
    // ======================================================
    [
        'keywords' => [
            'netflix', 'spotify', 'youtube premium', 'disney+',
            'viu', 'vidio', 'hbo', 'prime video',
            'steam', 'playstation', 'xbox', 'game',
            'bioskop', 'xxi', 'cgv', 'cinepolis',
            'konser', 'ticket', 'tiket.com', 'traveloka event',
            'nonton', 'film', 'we tv', 'iqiyi', 'bilibili',
            'nintendo', 'epic games', 'roblox', 'mobile legend',
            'free fire', 'pubg', 'valorant', 'genshin'
        ],
        'category_name' => 'Hiburan',
        'confidence' => 0.90,
        'is_active' => true,
        'created_by' => null,
    ],

    // ======================================================
    // HEALTH
    // ======================================================
    [
        'keywords' => [
            'dokter', 'rumah sakit', 'rs', 'klinik', 'puskesmas',
            'apotik', 'apotek', 'obat', 'vitamin', 'suplemen',
            'gym', 'fitness', 'sport center',
            'lab', 'cek darah', 'medical check up',
            'bpjs', 'asuransi kesehatan', 'halodoc', 'alodokter',
            'psikolog', 'terapi', 'vaksin', 'imunisasi',
            'masker', 'hand sanitizer', 'rapid test', 'pcr'
        ],
        'category_name' => 'Kesehatan',
        'confidence' => 0.90,
        'is_active' => true,
        'created_by' => null,
    ],

    // ======================================================
    // SHOPPING / RETAIL
    // ======================================================
    [
        'keywords' => [
            'indomaret', 'alfamart', 'alfamidi', 'lawson',
            'supermarket', 'hypermarket', 'lotte mart',
            'carrefour', 'hypermart', 'hero', 'giant',
            'pasar tradisional', 'pasar modern', 'sayur',
            'belanja bulanan', 'sembako', 'beras', 'gula', 'minyak',
            'sabun', 'shampoo', 'detergen', 'pewangi'
        ],
        'category_name' => 'Belanja Bulanan',
        'confidence' => 0.90,
        'is_active' => true,
        'created_by' => null,
    ],

    // ======================================================
    // EDUCATION
    // ======================================================
    [
        'keywords' => [
            'sekolah', 'sd', 'smp', 'sma', 'universitas',
            'kuliah', 'biaya pendidikan', 'spp', 'ukt',
            'les', 'privat', 'kursus', 'pelatihan', 'bootcamp',
            'ruangguru', 'zenius', 'binus', 'buku',
            'stationary', 'alat tulis', 'atk', 'komputer'
        ],
        'category_name' => 'Pendidikan',
        'confidence' => 0.90,
        'is_active' => true,
        'created_by' => null,
    ],

    // ======================================================
    // FASHION
    // ======================================================
    [
        'keywords' => [
            'zara', 'h&m', 'uniqlo', 'polo', 'adidas',
            'nike', 'converse', 'vans', 'gucci',
            'baju', 'celana', 'sepatu', 'kaos', 'kemeja',
            'jaket', 'jeans', 'hoodie', 'sweater', 'dress',
            'tas', 'dompet', 'topi', 'ikat pinggang'
        ],
        'category_name' => 'Pakaian',
        'confidence' => 0.85,
        'is_active' => true,
        'created_by' => null,
    ],

    // ======================================================
    // SAVINGS & INVESTMENT (EXPENSE SIDE)
    // ======================================================
    [
        'keywords' => [
            'nabung', 'tabungan', 'reksadana', 'saham',
            'emas', 'crypto', 'deposito', 'dana darurat',
            'sukuk', 'obligasi', 'p2p lending'
        ],
        'category_name' => 'Tabungan/Investasi',
        'confidence' => 0.90,
        'is_active' => true,
        'created_by' => null,
    ],

    // ======================================================
    // INCOME - SALARY
    // ======================================================
    [
        'keywords' => [
            'gaji', 'salary', 'payroll', 'upah', 'honor',
            'thr', 'bonus', 'insentif', 'komisi',
            'transfer gaji'
        ],
        'category_name' => 'Gaji Pokok',
        'confidence' => 0.95,
        'is_active' => true,
        'created_by' => null,
    ],

    // ======================================================
    // INCOME - FREELANCE
    // ======================================================
    [
        'keywords' => [
            'freelance', 'project', 'coding', 'design', 'ui ux',
            'writing', 'copywriting', 'content creator',
            'youtube', 'tiktok', 'adsense', 'upwork', 'fiverr'
        ],
        'category_name' => 'Pendapatan Freelance',
        'confidence' => 0.90,
        'is_active' => true,
        'created_by' => null,
    ],

    // ======================================================
    // INCOME - BUSINESS
    // ======================================================
    [
        'keywords' => [
            'jualan', 'reseller', 'dropship', 'olshop',
            'tokopedia', 'shopee', 'lazada', 'blibli',
            'bisnis', 'usaha', 'wirausaha'
        ],
        'category_name' => 'Bisnis Sampingan',
        'confidence' => 0.90,
        'is_active' => true,
        'created_by' => null,
    ],

    // ======================================================
    // INVESTMENT INCOME
    // ======================================================
    [
        'keywords' => [
            'dividen', 'saham', 'crypto', 'bitcoin',
            'trading', 'forex', 'capital gain',
            'investasi', 'profit', 'return'
        ],
        'category_name' => 'Investasi',
        'confidence' => 0.90,
        'is_active' => true,
        'created_by' => null,
    ],
];