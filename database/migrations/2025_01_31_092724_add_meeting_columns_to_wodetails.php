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
        Schema::table('wodetails', function (Blueprint $table) {
            $table->string('meeting_date_time')->nullable()->after('file_agreement');
            $table->string('google_meet_link')->nullable()->after('meeting_date_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wodetails', function (Blueprint $table) {
            //
        });
    }
};
