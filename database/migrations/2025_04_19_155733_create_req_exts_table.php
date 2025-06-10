<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('req_exts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tender_id');
            $table->integer('days');
            $table->text('reason');
            $table->string('client_org');
            $table->string('client_name');
            $table->string('client_email');
            $table->string('client_phone');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('req_exts');
    }
};
