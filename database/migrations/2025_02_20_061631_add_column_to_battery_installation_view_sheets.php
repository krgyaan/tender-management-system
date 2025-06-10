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
        Schema::table('battery_installation_view_sheets', function (Blueprint $table) {
            //
            $table->string('batteryinstallation_id')->nullable()->after('itemid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('battery_installation_view_sheets', function (Blueprint $table) {
            //
        });
    }
};
