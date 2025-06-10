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
        Schema::create('pay_on_portals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('emd_id')->unsigned();
            $table->string('purpose');
            $table->string('portal');
            $table->string('is_netbanking');
            $table->string('is_debit');
            $table->string('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pay_on_portals');
    }
};
