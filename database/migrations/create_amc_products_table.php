<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('amc_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amc_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained();
            $table->text('description')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_no')->nullable();
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('amc_products');
    }
};