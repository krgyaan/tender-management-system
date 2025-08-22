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
        Schema::create('tdsrecoveries', function (Blueprint $table) {
            $table->id();
             $table->foreignId('loneid')->nullable();
               $table->string('tds_amount')->nullable();
              $table->string('tds_document')->nullable();
              $table->string('tds_date')->nullable();
              $table->string('tdsrecoverybank_details')->nullable();
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
        Schema::dropIfExists('tdsrecoveries');
    }
};
