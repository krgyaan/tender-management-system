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
            Schema::table('loanadvances', function (Blueprint $table) {
                $table->string('banknoc_document')->nullable()->after('tdstobedeductedon_interest');
                $table->string('closurecreated_mca')->nullable()->after('tdstobedeductedon_interest');
                $table->enum('loan_close_status', [0, 1])->default(0)->after('tdstobedeductedon_interest');
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loanadvances', function (Blueprint $table) {
            //
        });
    }
};
