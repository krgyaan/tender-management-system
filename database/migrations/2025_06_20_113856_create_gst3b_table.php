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
        Schema::create('gst3b', function (Blueprint $table) {
            $table->id();
            $table->string('tally_data_link');
            $table->string('gst_2a_file_path')->nullable();
            $table->string('gst_tds_file_path')->nullable();
            $table->string('payment_challan_path')->nullable();
            $table->boolean('gst_tds_accepted')->default(0);
            $table->decimal('gst_tds_amount', 10, 2)->default(0.00);
            $table->boolean('gst_paid')->default(0);
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('filed_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gst3b');
    }
};
