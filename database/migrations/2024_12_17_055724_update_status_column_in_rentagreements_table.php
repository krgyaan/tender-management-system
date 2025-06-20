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
        Schema::table('rentagreements', function (Blueprint $table) {
            //
              $table->dropColumn('status');
               $table->tinyInteger('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentagreements', function (Blueprint $table) {
            //
             $table->enum('status', ['0', '1'])->default('1');  // Revert to ENUM with string values
        });
    }
};
