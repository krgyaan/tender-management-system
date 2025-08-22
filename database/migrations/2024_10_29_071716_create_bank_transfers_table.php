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
        Schema::create('bank_transfers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('emd_id')->unsigned();
            $table->string('bt_acc');
            $table->string('bt_ifsc');
            $table->string('bt_branch');
            $table->string('bt_acc_name');
            $table->timestamps();

            $table->foreign('emd_id')->references('id')->on('emds')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transfers');
    }
};
