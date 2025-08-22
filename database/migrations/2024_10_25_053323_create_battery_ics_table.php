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
        Schema::create('battery_ics', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('battery_id')->unsigned();
            $table->bigInteger('item_id')->unsigned();
            $table->integer('ic_name');
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->foreign('battery_id')->references('id')->on('battery_sheets')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('battery_sheet_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battery_ics');
    }
};
