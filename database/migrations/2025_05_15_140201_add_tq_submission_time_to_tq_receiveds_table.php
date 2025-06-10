<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tq_receiveds', function (Blueprint $table) {
            $table->string('tq_submission_time')->nullable();
        });
    }

    public function down()
    {
        Schema::table('tq_receiveds', function (Blueprint $table) {
            $table->dropColumn('tq_submission_time');
        });
    }
};
