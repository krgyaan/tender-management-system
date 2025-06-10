<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bt_tender_fees', function (Blueprint $table) {
            $table->id();
            $table->string('tender_no');
            $table->string('tender_name')->nullable();
            $table->date('due_date')->nullable();
            $table->string('purpose');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('ifsc');
            $table->string('amount');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bt_tender_fees');
    }
};
