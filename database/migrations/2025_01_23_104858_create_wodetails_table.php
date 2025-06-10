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
        Schema::create('wodetails', function (Blueprint $table) {
            $table->id();
             $table->foreignId('basic_detail_id')->nullable();
                $table->json('organization')->nullable();  
                $table->json('departments')->nullable();  
                $table->json('name')->nullable();          
                $table->json('phone')->nullable();       
                $table->json('email')->nullable();        
                $table->json('designation')->nullable();
                
                $table->foreignId('par_gst')->nullable();
                $table->string('max_ld')->nullable();
                $table->string('ldstartdate')->nullable();
                $table->string('maxlddate')->nullable();
                
             $table->enum('pbg_applicable_status', [0, 1])->default(0);
                $table->string('file_applicable')->nullable();
             
             $table->enum('contract_agreement_status', [0, 1])->default(0);
                 $table->string('file_agreement')->nullable();
                
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
        Schema::dropIfExists('wodetails');
    }
};
