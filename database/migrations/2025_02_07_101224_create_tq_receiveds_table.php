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
        Schema::create('tq_receiveds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->nullable();
            
            $table->json('tq_type')->nullable();  
            $table->json('description')->nullable();
            $table->string('tq_submission_date')->nullable();
            $table->string('tq_document')->nullable();
            
            $table->enum('status', [0, 1])->default(1);
            $table->string('ip')->nullable();
            $table->string('strtotime')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tq_receiveds');
    }
};
