<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('amc_site_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amc_site_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('organization')->nullable();
            $table->string('mobile');
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('amc_site_contacts');
    }
};