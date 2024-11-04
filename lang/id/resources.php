<?php

return [
    'category' => [
        'label' => 'Kategori',
        'name' => 'Nama',
        'description' => 'Deskripsi',
        'created_at' => 'Dibuat Pada',
        'updated_at' => 'Diperbarui Pada',
        'notifications' => [
            'import' => [
                'completed' => 'Impor kategori Anda telah selesai dan :count baris telah diimpor.',
                'failed' => ':count baris gagal diimpor.',
            ],
            'export' => [
                'completed' => 'Ekspor kategori Anda telah selesai dan :count baris telah diekspor.',
                'failed' => ':count baris gagal diekspor.',
            ],
        ],
    ],
    'contract' => [
        'label' => 'Kontrak',
        'supplier' => 'Pemasok',
        'start_date' => 'Tanggal Mulai',
        'end_date' => 'Tanggal Berakhir',
        'total_amount' => 'Total Nilai',
        'status' => 'Status',
        'created_at' => 'Dibuat Pada',
        'updated_at' => 'Diperbarui Pada',
    ],
    'product' => [
        'label' => 'Produk',
        'name' => 'Nama',
        'category' => 'Kategori',
        'barcode' => 'Barcode',
        'description' => 'Deskripsi',
        'price' => 'Harga',
        'created_at' => 'Dibuat Pada',
        'updated_at' => 'Diperbarui Pada',
        'notifications' => [
            'import' => [
                'completed' => 'Impor produk Anda telah selesai dan :count baris telah diimpor.',
                'failed' => ':count baris gagal diimpor.',
            ],
            'export' => [
                'completed' => 'Ekspor produk Anda telah selesai dan :count baris telah diekspor.',
                'failed' => ':count baris gagal diekspor.',
            ],
        ],
    ],
    'purchase' => [
        'label' => 'Pembelian',
        'supplier' => 'Pemasok',
        'product' => 'Produk',
        'purchase_date' => 'Tanggal Pembelian',
        'quantity' => 'Jumlah',
        'price' => 'Harga',
        'created_at' => 'Dibuat Pada',
        'updated_at' => 'Diperbarui Pada',
    ],
    'stock' => [
        'label' => 'Stok',
        'product' => 'Produk',
        'quantity' => 'Jumlah',
        'created_at' => 'Dibuat Pada',
        'updated_at' => 'Diperbarui Pada',
    ],
    'supplier' => [
        'label' => 'Pemasok',
        'name' => 'Nama',
        'contact_info' => 'Informasi Kontak',
        'created_at' => 'Dibuat Pada',
        'updated_at' => 'Diperbarui Pada',
        'notifications' => [
            'import' => [
                'completed' => 'Impor pemasok Anda telah selesai dan :count baris telah diimpor.',
                'failed' => ':count baris gagal diimpor.',
            ],
            'export' => [
                'completed' => 'Ekspor pemasok Anda telah selesai dan :count baris telah diekspor.',
                'failed' => ':count baris gagal diekspor.',
            ],
        ],
    ],
    'transaction' => [
        'label' => 'Transaksi',
        'product' => 'Produk',
        'transaction_type' => 'Tipe Transaksi',
        'quantity' => 'Jumlah',
        'transaction_date' => 'Tanggal Transaksi',
        'created_at' => 'Dibuat Pada',
        'updated_at' => 'Diperbarui Pada',
    ],
    'user' => [
        'label' => 'Pengguna',
        'name' => 'Nama',
        'email' => 'Email',
        'password' => 'Kata Sandi',
        'created_at' => 'Dibuat Pada',
        'updated_at' => 'Diperbarui Pada',
        'notifications' => [
            'import' => [
                'completed' => 'Impor pengguna Anda telah selesai dan :count baris telah diimpor.',
                'failed' => ':count baris gagal diimpor.',
            ],
            'export' => [
                'completed' => 'Ekspor pengguna Anda telah selesai dan :count baris telah diekspor.',
                'failed' => ':count baris gagal diekspor.',
            ],
        ],
        'avatar' => 'Avatar',
    ],
    'profile' => [
        'label' => 'Profil',
    ],
    'navigation' => [
        'groups' => [
            'master_data' => 'Data Master',
            'inventory' => 'Manajemen Inventori',
            'purchasing' => 'Pembelian',
            'access' => 'Manajemen Akses',
        ],
    ],
];
