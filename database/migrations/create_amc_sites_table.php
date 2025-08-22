<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('amc_sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amc_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('address');
            $table->string('map_link')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('amc_sites');
    }
};