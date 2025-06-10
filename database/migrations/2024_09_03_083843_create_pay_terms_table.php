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
        Schema::create('pay_terms', function (Blueprint $table) {
            $table->id();
            $table->string('tender_id');
            $table->string('payment_terms');
            $table->string('pbg');
            $table->string('pbg_duration');
            $table->string('bid_valid');
            $table->string('comm_eval');
            $table->string('maf_req');
            $table->bigInteger('delivery');
            $table->bigInteger('supply');
            $table->bigInteger('installation');
            $table->bigInteger('total');
            $table->bigInteger('ldperweek');
            $table->bigInteger('maxld');
            $table->string('phyDocs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pay_terms');
    }
};
