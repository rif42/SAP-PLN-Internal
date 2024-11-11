<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'categories',
            'contracts',
            'contract_items',
            'invoices',
            'invoice_products',
            'movements',
            'procurements',
            'procurement_products',
            'products',
            'product_stocks',
            'purchases',
            'shipping_documents',
            'shipping_document_products',
            'suppliers',
            'transactions',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'categories',
            'contracts',
            'contract_items',
            'invoices',
            'invoice_products',
            'movements',
            'procurements',
            'procurement_products',
            'products',
            'product_stocks',
            'purchases',
            'shipping_documents',
            'shipping_document_products',
            'suppliers',
            'transactions',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
}; 
