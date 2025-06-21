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
        Schema::create('gstr1', function (Blueprint $table) {
            $table->id();
            $table->string('gst_r1_sheet_path');
            $table->string('tally_data_link');
            $table->boolean('confirmation')->default(false);
            $table->string('return_file_path')->nullable();
            $table->timestamp('filed_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gstr1');
    }
};
