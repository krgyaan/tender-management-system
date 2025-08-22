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
        Schema::create('rentagreements', function (Blueprint $table) {
            $table->id();
            $table->string('first_party')->nullable(); // A string for the first party name, nullable
$table->string('second_party')->nullable(); // A string for the second party name, nullable
$table->string('rent_amount')->nullable(); // A string for the rent amount, nullable
$table->string('security_deposit')->nullable(); // A string for the security deposit, nullable
$table->string('start_date')->nullable(); // A string for the start date, nullable
$table->string('end_date')->nullable(); // A string for the end date, nullable
$table->string('rent_increment_at_expiry')->nullable(); // A string for rent increment at expiry, nullable
$table->string('image')->nullable(); // A string for an image, nullable (could be a path to an image file)
$table->string('remarks')->nullable(); // A string for additional remarks, nullable
$table->enum('status', [0, 1])->default(1); // An enum field for the status, with 0 or 1 values, default is 1
$table->string('ip')->nullable(); // A string for the IP address, nullable
$table->string('strtotime')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentagreements');
    }
};
