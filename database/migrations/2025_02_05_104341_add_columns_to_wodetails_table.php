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
        $table->string('upload_mom')->nullable()->after('google_meet_link');
        $table->string('contract_agreement')->nullable()->after('upload_mom');
        $table->string('client_signed')->nullable()->after('contract_agreement');
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
