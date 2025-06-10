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
        Schema::create('battery_sheet_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('battery_id')->constrained()->onDelete('cascade'); // Foreign key to batteries table
            $table->string('item_name');
            $table->string('model');
            $table->decimal('ah', 8, 2);
            $table->integer('cells');
            $table->integer('spare_cell');
            $table->decimal('price', 10, 2);
            $table->integer('banks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battery_sheet_items');
    }
};
