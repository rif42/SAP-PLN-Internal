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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('contact_info');

            $table->string('sales_name');
            $table->string('sales_phone');
            $table->string('sales_email')->nullable();
            $table->string('logistics_name');
            $table->string('logistics_phone');
            $table->string('logistics_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn([
                'sales_name',
                'sales_phone',
                'sales_email',
                'logistics_name',
                'logistics_phone',
                'logistics_email'
            ]);

            $table->string('contact_info');
        });
    }
};
