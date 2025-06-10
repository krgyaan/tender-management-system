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
        Schema::create('rfqs', function (Blueprint $table) {
            $table->id();
            $table->string('tender_id');
            $table->string('team_name');
            $table->string('organisation');
            $table->string('location');
            $table->string('item_name');
            $table->string('techical');
            $table->string('boq');
            $table->string('scope');
            $table->string('maf');
            $table->string('docs_list');
            $table->string('mii');
            $table->string('requirements');
            $table->string('qty');
            $table->string('unit');
            $table->string('due_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfqs');
    }
};
