<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dd_tender_fees', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tender_id')->unsigned();
            $table->string('tender_name');
            $table->string('dd_needed_in');
            $table->string('purpose_of_dd');
            $table->string('in_favour_of');
            $table->date('dd_payable_at');
            $table->decimal('dd_amount', 20, 2);
            $table->string('courier_address');
            $table->dateTime('delivery_date_time');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dd_tender_fees');
    }
};
