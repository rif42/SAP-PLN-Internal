<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('procurements', function (Blueprint $table) {
            $table->string('dkmj_document')->nullable()->after('status_at');
        });
    }

    public function down()
    {
        Schema::table('procurements', function (Blueprint $table) {
            $table->dropColumn('dkmj_document');
        });
    }
};
