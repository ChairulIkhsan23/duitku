<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Categories System
    |--------------------------------------------------------------------------
    | Data ini akan digunakan untuk seed kategori default sistem.
    | Jangan tambahkan id / user_id di sini agar reusable.
    */

    // =========================
    // INCOME CATEGORIES
    // =========================
    [
        'name' => 'Gaji Pokok',
        'type' => 'income',
        'icon' => 'FaWallet',
        'color' => '#4CAF50',
        'budget_default' => null,
        'is_default' => true,
    ],
    [
        'name' => 'Pendapatan Freelance',
        'type' => 'income',
        'icon' => 'FaLaptopCode',
        'color' => '#2196F3',
        'budget_default' => null,
        'is_default' => true,
    ],
    [
        'name' => 'Investasi',
        'type' => 'income',
        'icon' => 'FaChartLine',
        'color' => '#9C27B0',
        'budget_default' => null,
        'is_default' => true,
    ],
    [
        'name' => 'Bisnis Sampingan',
        'type' => 'income',
        'icon' => 'FaStore',
        'color' => '#FF9800',
        'budget_default' => null,
        'is_default' => true,
    ],
    [
        'name' => 'Cashback / Reward',
        'type' => 'income',
        'icon' => 'FaGift',
        'color' => '#E91E63',
        'budget_default' => null,
        'is_default' => true,
    ],

    // =========================
    // EXPENSE CATEGORIES
    // =========================
    [
        'name' => 'Makan & Minum',
        'type' => 'expense',
        'icon' => 'FaUtensils',
        'color' => '#FF5722',
        'budget_default' => 1500000,
        'is_default' => true,
    ],
    [
        'name' => 'Transportasi',
        'type' => 'expense',
        'icon' => 'FaCar',
        'color' => '#2196F3',
        'budget_default' => 500000,
        'is_default' => true,
    ],
    [
        'name' => 'Hunian',
        'type' => 'expense',
        'icon' => 'FaHome',
        'color' => '#795548',
        'budget_default' => 2000000,
        'is_default' => true,
    ],
    [
        'name' => 'Belanja Bulanan',
        'type' => 'expense',
        'icon' => 'FaShoppingCart',
        'color' => '#FF9800',
        'budget_default' => 800000,
        'is_default' => true,
    ],
    [
        'name' => 'Kesehatan',
        'type' => 'expense',
        'icon' => 'FaHeartbeat',
        'color' => '#E91E63',
        'budget_default' => 300000,
        'is_default' => true,
    ],
    [
        'name' => 'Hiburan',
        'type' => 'expense',
        'icon' => 'FaFilm',
        'color' => '#9C27B0',
        'budget_default' => 500000,
        'is_default' => true,
    ],
    [
        'name' => 'Pendidikan',
        'type' => 'expense',
        'icon' => 'FaGraduationCap',
        'color' => '#00BCD4',
        'budget_default' => 1000000,
        'is_default' => true,
    ],
    [
        'name' => 'Lain-lain',
        'type' => 'expense',
        'icon' => 'FaEllipsisH',
        'color' => '#9E9E9E',
        'budget_default' => 100000,
        'is_default' => true,
    ],
];