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
        Schema::create('wo_acceptance_yes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basic_detail_id')->nullable();
            $table->enum('wo_yes', [1])->default(1);
            
            $table->json('page_no')->nullable();  
            $table->json('clause_no')->nullable();  
            $table->json('current_statement')->nullable();          
            $table->json('corrected_statement')->nullable();       
           
                
            $table->string('followup_frequency')->nullable();
            $table->string('stop_opsans')->nullable();
            $table->string('text')->nullable();
            $table->string('image')->nullable();
            $table->text('remark')->nullable();
                
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
        Schema::dropIfExists('wo_acceptance_yes');
    }
};
