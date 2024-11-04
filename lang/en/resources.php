<?php

return [
    'category' => [
        'label' => 'Categories',
        'title' => 'Title',
        'description' => 'Description', 
        'created_at' => 'Created At',
    ],
    'contract' => [
        'label' => 'Contracts',
        'supplier' => 'Supplier',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'total_amount' => 'Total Amount',
        'status' => 'Status',
        'created_at' => 'Created At',
    ],
    'product' => [
        'label' => 'Products',
        'name' => 'Name',
        'category' => 'Category',
        'barcode' => 'Barcode',
        'description' => 'Description',
        'price' => 'Price',
        'created_at' => 'Created At',
        'notifications' => [
            'import' => [
                'completed' => 'Your product import has completed and :count rows were imported.',
                'failed' => ':count rows failed to import.',
            ],
            'export' => [
                'completed' => 'Your product export has completed and :count rows were exported.',
                'failed' => ':count rows failed to export.',
            ],
        ],
    ],
    'purchase' => [
        'label' => 'Purchases',
        'supplier' => 'Supplier',
        'product' => 'Product',
        'purchase_date' => 'Purchase Date',
        'quantity' => 'Quantity',
        'price' => 'Price',
        'created_at' => 'Created At',
    ],
    'stock' => [
        'label' => 'Stock',
        'product' => 'Product',
        'quantity' => 'Quantity',
        'created_at' => 'Created At',
    ],
    'supplier' => [
        'label' => 'Suppliers',
        'name' => 'Name',
        'contact_info' => 'Contact Info',
        'created_at' => 'Created At',
        'notifications' => [
            'import' => [
                'completed' => 'Your supplier import has completed and :count rows were imported.',
                'failed' => ':count rows failed to import.',
            ],
            'export' => [
                'completed' => 'Your supplier export has completed and :count rows were exported.',
                'failed' => ':count rows failed to export.',
            ],
        ],
    ],
    'transaction' => [
        'label' => 'Transactions',
        'product' => 'Product',
        'transaction_type' => 'Transaction Type',
        'quantity' => 'Quantity',
        'transaction_date' => 'Transaction Date',
        'created_at' => 'Created At',
    ],
    'user' => [
        'label' => 'Users',
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'notifications' => [
            'import' => [
                'completed' => 'Your user import has completed and :count rows were imported.',
                'failed' => ':count rows failed to import.',
            ],
            'export' => [
                'completed' => 'Your user export has completed and :count rows were exported.',
                'failed' => ':count rows failed to export.',
            ],
        ],
        'avatar' => 'Avatar',
    ],
    'profile' => [
        'label' => 'Profil',
    ],
    'navigation' => [
        'groups' => [
            'master_data' => 'Master Data',
            'inventory' => 'Inventory Management',
            'purchasing' => 'Purchasing',
            'access' => 'Access Management',
        ],
    ],
];
