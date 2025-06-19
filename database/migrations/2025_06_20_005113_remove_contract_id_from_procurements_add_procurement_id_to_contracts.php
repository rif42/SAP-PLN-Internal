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
        // First, add procurement_id to contracts table
        Schema::table('contracts', function (Blueprint $table) {
            $table->foreignId('procurement_id')->nullable()->constrained('procurements')->onDelete('cascade');
        });

        // Then remove contract_id from procurements table
        Schema::table('procurements', function (Blueprint $table) {
            $table->dropForeign(['contract_id']);
            $table->dropColumn('contract_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, add contract_id back to procurements table
        Schema::table('procurements', function (Blueprint $table) {
            $table->foreignId('contract_id')->nullable()->constrained('contracts')->onDelete('cascade');
        });

        // Then remove procurement_id from contracts table
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['procurement_id']);
            $table->dropColumn('procurement_id');
        });
    }
};
