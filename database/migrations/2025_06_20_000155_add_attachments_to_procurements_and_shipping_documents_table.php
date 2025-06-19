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
            $table->json('attachments')->nullable()->after('status_at');
        });

        Schema::table('shipping_documents', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('status_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('procurements', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });

        Schema::table('shipping_documents', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });
    }
};
