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
        Schema::create('courier_dashboards', function (Blueprint $table) {
            $table->id();
            $table->string('to_org');
            $table->string('to_name');
            $table->string('to_addr');
            $table->string('to_pin');
            $table->string('to_mobile');
            $table->bigInteger('emp_from');
            $table->date('del_date');
            $table->integer('urgency');
            $table->string('courier_docs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courier_dashboards');
    }
};
