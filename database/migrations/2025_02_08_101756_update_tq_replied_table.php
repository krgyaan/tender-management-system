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
        Schema::table('tq_replieds', function (Blueprint $table) {
            $table->time('tq_submission_time')->nullable()->after('tq_submission_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tq_replieds', function (Blueprint $table) {
            //
        });
    }
};
