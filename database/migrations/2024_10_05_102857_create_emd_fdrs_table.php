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
        Schema::create('emd_fdrs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('emd_id');
            $table->string('fdr_purpose');
            $table->string('fdr_favour');
            $table->string('fdr_amt');
            $table->string('fdr_expiry');
            $table->string('fdr_needs');
            $table->string('fdr_bank_name');
            $table->string('fdr_bank_acc');
            $table->string('fdr_bank_ifsc');
            $table->string('fdr_status');
            $table->string('fdr_rejection')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emd_fdrs');
    }
};
