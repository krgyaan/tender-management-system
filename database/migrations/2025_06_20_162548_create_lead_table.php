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
        // Leads table
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('name');
            $table->string('designation');
            $table->string('phone');
            $table->string('email');
            $table->string('address');
            $table->string('state');
            $table->string('type');
            $table->string('industry');
            $table->string('team');
            $table->text('points_discussed')->nullable();
            $table->text('ve_responsibility')->nullable();
            $table->timestamps();
        });

        // Lead Industry table
        Schema::create('lead_industries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->enum('status', ['1', '0'])->default('1');
            $table->timestamps();
        });

        // Lead Type table
        Schema::create('lead_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->enum('status', ['1', '0'])->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_industries');
        Schema::dropIfExists('lead_types');
        Schema::dropIfExists('leads');
    }
};
