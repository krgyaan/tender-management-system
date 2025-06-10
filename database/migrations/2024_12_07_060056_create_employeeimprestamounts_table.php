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
        Schema::create('employeeimprestamounts', function (Blueprint $table) {
            $table->id();
                $table->foreignId('name_id');
    $table->date('date');
    $table->string('team_member_name');
    $table->string('amount');
    $table->string('project_name');
    $table->enum('status', [0, 1])->default(1);
     $table->string('ip')->nullable();
    $table->string('strtotime')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employeeimprestamounts');
    }
};
