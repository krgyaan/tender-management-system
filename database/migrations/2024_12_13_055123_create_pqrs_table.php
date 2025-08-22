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
        Schema::create('pqrs', function (Blueprint $table) {
      $table->id();
$table->string('team_name')->nullable();
$table->string('project_name')->nullable();
$table->string('value')->nullable();
$table->string('item')->nullable();
$table->date('po_date');
$table->string('uplode_po')->nullable();
$table->date('sap_gem_po_date');
$table->string('uplode_sap_gem_po')->nullable();
$table->date('completion_date');
$table->string('uplode_completion')->nullable();
$table->string('performace_cretificate')->nullable();
$table->string('remarks')->nullable();
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
        Schema::dropIfExists('pqrs');
    }
};
