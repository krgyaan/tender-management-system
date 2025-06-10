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
        Schema::create('wo_acceptances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basic_detail_id')->nullable();
            $table->enum('wo_no', [0])->default(0);
            $table->string('accepted_initiate')->nullable();
            $table->string('accepted_signed')->nullable();
            $table->string('accepted_and_signed')->nullable();
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
        Schema::dropIfExists('wo_acceptances');
    }
};
