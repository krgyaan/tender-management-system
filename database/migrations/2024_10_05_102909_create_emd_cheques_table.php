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
        Schema::create('emd_cheques', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('emd_id');
            $table->string('cheque_favour');
            $table->string('cheque_amt');
            $table->string('cheque_date');
            $table->string('cheque_needs');
            $table->string('cheque_reason');
            $table->string('cheque_bank');
            $table->string('cheque_status');
            $table->string('cheque_rejection')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emd_cheques');
    }
};
