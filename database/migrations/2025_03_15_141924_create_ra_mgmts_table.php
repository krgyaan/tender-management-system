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
        Schema::create('ra_mgmts', function (Blueprint $table) {
            $table->id();
            $table->string('tender_no');
            $table->date('bid_submission_date')->nullable();
            $table->enum('status', ['Under Evaluation', 'RA Scheduled', 'Disqualified', 'RA Started', 'Won', 'Lost', 'Lost - H1 Elimination',])->default('Under Evaluation');
            $table->boolean('technically_qualified')->nullable();
            $table->text('disqualification_reason')->nullable();
            $table->json('qualified_parties')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->decimal('start_price', 15, 2)->nullable();
            $table->decimal('close_price', 15, 2)->nullable();
            $table->timestamp('close_time')->nullable();
            $table->enum('result', ['Won', 'Lost', 'H1 Elimination'])->nullable();
            $table->boolean('ve_start_of_ra')->nullable();
            $table->text('screenshot_qualified_parties')->nullable();
            $table->text('screenshot_decrements')->nullable();
            $table->text('final_result_screenshot')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ra_mgmts');
    }
};
