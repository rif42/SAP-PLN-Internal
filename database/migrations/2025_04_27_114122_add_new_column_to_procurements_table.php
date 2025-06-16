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
        Schema::table('procurements', function (Blueprint $table) {
            // Drop the contract_id column
            $table->dropForeign(['contract_id']);
            $table->dropColumn('contract_id');

            // Add new columns
            $table->bigInteger('amp_id')->nullable();
            $table->string('penugasan_id')->nullable();
            $table->string('kategori')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('procurements', function (Blueprint $table) {
            // Remove the new columns
            $table->dropColumn(['amp_id', 'penugasan_id', 'kategori']);

            // Add back the contract_id column
            $table->foreignId('contract_id')->nullable()->constrained('contracts')->onDelete('cascade');
        });
    }
};

