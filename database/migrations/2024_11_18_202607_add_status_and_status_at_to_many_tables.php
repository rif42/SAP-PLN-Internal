<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
            Schema::table('procurements', function (Blueprint $table) {
                $table->enum('status', ['pending', 'done', 'canceled'])->default('pending');
                $table->timestamp('status_at')->nullable();
            });

            Schema::table('procurement_products', function (Blueprint $table) {
                $table->enum('status', ['pending', 'done', 'canceled'])->default('pending');
                $table->timestamp('status_at')->nullable();
            });

            Schema::table('purchases', function (Blueprint $table) {
                $table->enum('status', ['pending', 'done', 'canceled'])->default('pending');
                $table->timestamp('status_at')->nullable();
            });

            Schema::table('purchase_products', function (Blueprint $table) {
                $table->enum('status', ['pending', 'done', 'canceled'])->default('pending');
                $table->timestamp('status_at')->nullable();
            });

            Schema::table('invoices', function (Blueprint $table) {
                $table->enum('status', ['pending', 'done', 'canceled'])->default('pending');
                $table->timestamp('status_at')->nullable();
            });

            Schema::table('invoice_products', function (Blueprint $table) {
                $table->enum('status', ['pending', 'done', 'canceled'])->default('pending');
                $table->timestamp('status_at')->nullable();
            });

            Schema::table('shipping_documents', function (Blueprint $table) {
                $table->enum('status', ['pending', 'done', 'canceled'])->default('pending');
                $table->timestamp('status_at')->nullable();
            });

            Schema::table('shipping_document_products', function (Blueprint $table) {
                $table->enum('status', ['pending', 'done', 'canceled'])->default('pending');
                $table->timestamp('status_at')->nullable();
            });

            Schema::table('contracts', function (Blueprint $table) {
                $table->timestamp('status_at')->nullable();
            });

            Schema::table('contract_products', function (Blueprint $table) {
                $table->enum('status', ['pending', 'done', 'canceled'])->default('pending');
                $table->timestamp('status_at')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            Schema::table('procurements', function (Blueprint $table) {
                $table->dropColumn('status');
                $table->dropColumn('status_at');
            });

            Schema::table('procurement_products', function (Blueprint $table) {
                $table->dropColumn('status');
                $table->dropColumn('status_at');
            });

            Schema::table('purchases', function (Blueprint $table) {
                $table->dropColumn('status');
                $table->dropColumn('status_at');
            });

            Schema::table('purchase_products', function (Blueprint $table) {
                $table->dropColumn('status');
                $table->dropColumn('status_at');
            });

            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('status');
                $table->dropColumn('status_at');
            });

            Schema::table('invoice_products', function (Blueprint $table) {
                $table->dropColumn('status');
                $table->dropColumn('status_at');
            });

            Schema::table('shipping_documents', function (Blueprint $table) {
                $table->dropColumn('status');
                $table->dropColumn('status_at');
            });

            Schema::table('shipping_document_products', function (Blueprint $table) {
                $table->dropColumn('status');
                $table->dropColumn('status_at');
            });

            Schema::table('contracts', function (Blueprint $table) {
                $table->dropColumn('status_at');
            });

            Schema::table('contract_products', function (Blueprint $table) {
                $table->dropColumn('status');
                $table->dropColumn('status_at');
            });
    }
};
