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
        Schema::create('loanadvances', function (Blueprint $table) {
            $table->id();
             $table->string('loanparty_name')->nullable();
              $table->string('bank_name')->nullable();
              $table->string('typeof_loan')->nullable();
              $table->string('loanamount')->nullable();
              $table->string('sanctionletter_date')->nullable();
              $table->string('emipayment_date')->nullable();
              $table->string('sanction_letter')->nullable();
              $table->string('bankloan_schedule')->nullable();
              $table->string('loan_schedule')->nullable();
              $table->string('chargemca_website')->nullable();
              $table->string('tdstobedeductedon_interest')->nullable();
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
        Schema::dropIfExists('loanadvances');
    }
};
