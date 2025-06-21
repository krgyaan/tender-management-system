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
        Schema::create('checklist', function (Blueprint $table) {
            $table->id();
            $table->string('task_name');
            $table->string('frequency');
            $table->string('responsibility');
            $table->string('responsibility_timer')->nullable();
            $table->string('accountability');
            $table->string('accountability_timer')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->boolean('responsibility_completed')->default(0);
            $table->boolean('accountability_completed')->default(0);
            $table->text('responsibility_remark')->nullable();
            $table->dateTime('responsibility_remark_date')->nullable();
            $table->text('accountability_remark')->nullable();
            $table->dateTime('accountability_remark_date')->nullable();
            $table->string('final_result_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist');
    }
};
