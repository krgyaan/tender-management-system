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
        Schema::create('emd_bgs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('emd_id');
            $table->string('bg_favour');
            $table->string('bg_address');
            $table->string('bg_expiry');
            $table->string('bg_amt');
            $table->string('bg_cont_percent');
            $table->string('bg_fdr_percent');
            $table->string('bg_needs');
            $table->string('bg_stamp');
            $table->string('bg_courier_addr');
            $table->string('bg_format_doer');
            $table->string('bg_format_imran');
            $table->string('bg_po');
            $table->string('bg_client_user');
            $table->string('bg_client_cp');
            $table->string('bg_client_fin');
            $table->string('bg_bank_name');
            $table->string('bg_bank_acc');
            $table->string('bg_bank_ifsc');
            $table->string('bg_status');
            $table->string('bg_rejection')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emd_bgs');
    }
};
