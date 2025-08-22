<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pop_tender_fees', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tender_id')->unsigned();
            $table->string('tender_name');
            $table->dateTime('due_date_time');
            $table->string('purpose');
            $table->string('portal_name');
            $table->string('netbanking_available');
            $table->string('bank_debit_card');
            $table->decimal('amount', 20, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pop_tender_fees');
    }
};
