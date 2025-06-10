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
        Schema::create('follow_up_persons', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('follwup_id')->unsigned();
            $table->string('org');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->foreign('follwup_id')->references('id')->on('follow_ups')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_up_persons');
    }
};
