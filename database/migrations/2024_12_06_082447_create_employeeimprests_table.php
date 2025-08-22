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
        Schema::create('employeeimprests', function (Blueprint $table) {
           $table->foreignId('name_id');
$table->string('party_name');
$table->string('project_name');
$table->string('amount');
$table->foreignId('category_id');
$table->foreignId('team_id');
$table->string('invoice_proof');
$table->string('remark');
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
        Schema::dropIfExists('employeeimprests');
    }
};
