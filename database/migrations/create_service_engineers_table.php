<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('amc_service_engineers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amc_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('organization')->nullable();
            $table->string('mobile');
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_engineers');
    }
};