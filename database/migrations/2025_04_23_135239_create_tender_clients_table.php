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
        Schema::create('tender_clients', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tender_id');
            $table->string('client_name')->nullable();
            $table->string('client_designation')->nullable();
            $table->string('client_mobile')->nullable();
            $table->string('client_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_clients');
    }
};
