<?php

return [

    [
        'slug' => 'first-step',
        'name' => 'First Step',
        'description' => 'Mencatat transaksi pertama Anda',
        'requirement' => [
            'type' => 'transaction_count',
            'min' => 1,
        ],
        'icon' => 'FaWalking',
        'color' => '#4CAF50',
        'points' => 10,
        'trigger' => 'transaction',
    ],

    [
        'slug' => 'budget-ninja',
        'name' => 'Budget Ninja',
        'description' => 'Tidak overspend selama 30 hari berturut-turut',
        'requirement' => [
            'type' => 'no_overspend_days',
            'days' => 30,
        ],
        'icon' => 'FaShieldAlt',
        'color' => '#2196F3',
        'points' => 50,
        'trigger' => 'transaction',
    ],

    [
        'slug' => 'early-bird',
        'name' => 'Early Bird',
        'description' => 'Mencatat transaksi 7x sebelum jam 9 pagi',
        'requirement' => [
            'type' => 'morning_transaction',
            'count' => 7,
        ],
        'icon' => 'FaSun',
        'color' => '#FFC107',
        'points' => 25,
        'trigger' => 'transaction',
    ],

    [
        'slug' => 'saver-king',
        'name' => 'Saver King',
        'description' => 'Menabung lebih dari 30% dari income selama 3 bulan',
        'requirement' => [
            'type' => 'savings_rate',
            'percentage' => 30,
            'months' => 3,
        ],
        'icon' => 'FaPiggyBank',
        'color' => '#FF9800',
        'points' => 100,
        'trigger' => 'transaction',
    ],

    [
        'slug' => 'category-master',
        'name' => 'Category Master',
        'description' => 'Menggunakan semua kategori minimal 1x',
        'requirement' => [
            'type' => 'all_categories_used',
        ],
        'icon' => 'FaThLarge',
        'color' => '#9C27B0',
        'points' => 75,
        'trigger' => 'transaction',
    ],

    [
        'slug' => 'night-owl',
        'name' => 'Night Owl',
        'description' => '5x transaksi malam dengan nominal > 500.000',
        'requirement' => [
            'type' => 'night_transaction',
            'count' => 5,
            'amount' => 500000,
        ],
        'icon' => 'FaMoon',
        'color' => '#3F51B5',
        'points' => 30,
        'trigger' => 'transaction',
    ],

    [
        'slug' => 'streak-legend',
        'name' => 'Streak Legend',
        'description' => 'Mencapai streak 60 hari berturut-turut',
        'requirement' => [
            'type' => 'streak',
            'days' => 60,
        ],
        'icon' => 'FaCrown',
        'color' => '#FFD700',
        'points' => 200,
        'trigger' => 'transaction',
    ],

    [
        'slug' => 'data-analyst',
        'name' => 'Data Analyst',
        'description' => 'Generate laporan 10x',
        'requirement' => [
            'type' => 'report_count',
            'count' => 10,
        ],
        'icon' => 'FaChartLine',
        'color' => '#00BCD4',
        'points' => 40,
        'trigger' => 'transaction',
    ],

];