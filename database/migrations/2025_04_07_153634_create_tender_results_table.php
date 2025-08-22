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
        Schema::create('tender_results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tender_id');
            $table->string('technically_qualified');
            $table->text('disqualification_reason');
            $table->string('qualified_parties_count');
            $table->string('qualified_parties_names');
            $table->string('result');
            $table->decimal('l1_price', 15, 2);
            $table->decimal('l2_price', 15, 2);
            $table->decimal('our_price', 15, 2);
            $table->string('qualified_parties_screenshot');
            $table->string('final_result');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_results');
    }
};
