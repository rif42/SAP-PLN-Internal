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
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');

            $table->dropColumn('quantity');
            $table->dropColumn('price');

            $table->string('code')->unique()->nullable();
            $table->string('number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);

            $table->dropColumn('code');
            $table->dropColumn('number');
        });
    }
};
