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
        Schema::create('fixed_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('party_name');
            $table->enum('amount_type', ['Fixed', 'Variable']);
            $table->decimal('amount', 15, 2)->nullable();
            $table->enum('payment_method', ['Auto Debit', 'Bank Transfer']);
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc')->nullable();
            $table->date('due_date');
            $table->string('frequency');
            $table->string('status')->nullable();
            $table->text('utr_message')->nullable();
            $table->timestamp('payment_datetime')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_expenses');
    }
};
