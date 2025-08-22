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
        Schema::create('tds', function (Blueprint $table) {
            $table->id();
            $table->string('tds_excel_path')->nullable();
            $table->string('tally_data_link');
            $table->string('tds_challan_path')->nullable();
            $table->string('tds_payment_challan_path')->nullable();
            $table->string('tds_return_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tds');
    }
};