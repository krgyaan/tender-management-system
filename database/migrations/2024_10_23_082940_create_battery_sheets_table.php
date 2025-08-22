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
        Schema::create('battery_sheets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tender_id');
            $table->integer('sheet_type');
            $table->string('bg');
            $table->decimal('freight_per', 8, 2);
            $table->decimal('cash_margin', 8, 2);
            $table->decimal('gst_battery', 5, 2);
            $table->decimal('gst_ic', 5, 2);
            $table->decimal('gst_buyback', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battery_sheets');
    }
};
