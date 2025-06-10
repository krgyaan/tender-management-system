<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->string('area')->nullable();
            $table->string('party_name')->nullable();
            $table->string('followup_for')->nullable();
            $table->string('amount')->nullable();
            $table->text('details')->nullable();
            $table->bigInteger('assigned_to')->nullable();
            $table->string('frequency')->nullable();
            $table->string('stop_reason')->nullable();
            $table->string('proof_text')->nullable();
            $table->string('proof_img')->nullable();
            $table->string('stop_rem')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
