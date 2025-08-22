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
        Schema::create('rfq_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rfq_id');
            $table->dateTime('receipt_datetime');
            $table->decimal('gst_percentage', 5, 2);
            $table->enum('gst_type', ['inclusive', 'extra']);
            $table->integer('delivery_time');
            $table->enum('freight_type', ['inclusive', 'extra']);
            $table->string('quotation_document');
            $table->string('technical_documents')->nullable();
            $table->string('maf_document')->nullable();
            $table->string('mii_document')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfq_responses');
    }
};
