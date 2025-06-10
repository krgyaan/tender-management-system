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
        Schema::create('eligible_docs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tender_id');
            $table->bigInteger('info_id');
            $table->string('doc_name');
            $table->string('doc_file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eligible_docs');
    }
};
