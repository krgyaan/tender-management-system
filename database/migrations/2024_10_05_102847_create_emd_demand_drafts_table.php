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
        Schema::create('emd_demand_drafts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('emd_id');
            $table->string('dd_favour');
            $table->string('dd_amt');
            $table->string('dd_payable');
            $table->string('dd_needs');
            $table->string('dd_status');
            $table->string('dd_rejection')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emd_demand_drafts');
    }
};
