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
        Schema::create('quotation_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_receipt_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained();
            $table->text('description')->nullable();
            $table->decimal('quantity', 10, 2);
            $table->string('unit', 50);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_receipt_items');
    }
};
