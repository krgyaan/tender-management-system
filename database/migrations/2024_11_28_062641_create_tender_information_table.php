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
        Schema::create('tender_information', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('tender_id')->unsigned();
            $table->string('is_rejectable')->nullable();
            $table->string('reject_reason')->nullable();
            $table->string('reject_remarks')->nullable();
            $table->string('emd_red')->nullable();
            $table->string('rev_auction')->nullable();
            $table->string('pt_supply')->nullable();
            $table->string('pt_ic')->nullable();
            $table->string('pbg')->nullable();
            $table->string('pbg_duration')->nullable();
            $table->string('bid_valid')->nullable();
            $table->string('comm_eval')->nullable();
            $table->string('maf_req')->nullable();
            $table->string('supply')->nullable();
            $table->string('installation')->nullable();
            $table->string('ldperweek')->nullable();
            $table->string('maxld')->nullable();
            $table->string('phyDocs')->nullable();
            $table->string('dead_date')->nullable();
            $table->string('dead_time')->nullable();
            $table->string('tech_eligible')->nullable();
            $table->string('order1')->nullable();
            $table->string('order2')->nullable();
            $table->string('order3')->nullable();
            $table->string('aat')->nullable();
            $table->string('aat_amt')->nullable();
            $table->string('wc')->nullable();
            $table->string('wc_amt')->nullable();
            $table->string('sc')->nullable();
            $table->string('sc_amt')->nullable();
            $table->string('nw')->nullable();
            $table->string('nw_amt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_information');
    }
};
