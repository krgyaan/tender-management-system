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
        Schema::create('private_costing_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enquiry_id')->constrained()->onDelete('cascade');
            $table->foreignId('prepared_by')->constrained('users')->onDelete('cascade');
            $table->decimal('final_price', 12, 2);
            $table->decimal('gst_percentage', 5, 2);
            $table->decimal('receipt_pre_gst', 12, 2);
            $table->decimal('budget_pre_gst', 12, 2);
            $table->decimal('gross_margin', 5, 2);
            $table->decimal('margin_value', 12, 2);
            $table->json('documents')->nullable(); // Store file paths as JSON array
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_costing_sheets');
    }
};
