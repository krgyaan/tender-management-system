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
        Schema::create('battery_item_prices', function (Blueprint $table) {
            $table->id();
            $table->string('freight_age')->nullable();
            $table->string('bg')->nullable();
            $table->string('cash_margin')->nullable();
            $table->string('buyback')->nullable();
            $table->string('actual_buyback')->nullable();
            $table->string('gst_on_battery')->nullable();
            $table->string('gst_on_ic')->nullable();
            $table->string('gst_on_buyback')->nullable();
            $table->string('item_name')->nullable();
            $table->string('item_model')->nullable();
            $table->string('ah')->nullable();
            $table->string('cells_per_bank')->nullable();
            $table->string('spare_cells')->nullable();
            $table->string('price_ah')->nullable();
            $table->string('bidding_installtion_cost')->nullable();
            $table->string('no_of_banks')->nullable();
            $table->string('old_battery_bank')->nullable();
            $table->enum('status',['0','1'])->default('1');
            $table->string('ip')->nullable();
            $table->string('strtotiem')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battery_item_prices');
    }
};
