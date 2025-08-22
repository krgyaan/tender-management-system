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
        Schema::create('battery_installation_view_sheets', function (Blueprint $table) {
            $table->id();
            $table->string('itemid')->nullable();
            $table->string('batteryinstallation_value')->nullable();
            $table->enum('status',['0','1'])->default('1');
            $table->string('ip')->nullable();
            $table->string('strtotime')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battery_installation_view_sheets');
    }
};
