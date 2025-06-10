<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Insert the predefined statuses
        DB::table('statuses')->insert([
            ['name' => 'Under preparation'],
            ['name' => 'Missed'],
            ['name' => 'Not Allowed by OEM'],
            ['name' => 'Not Eligible'],
            ['name' => 'Product type bid'],
            ['name' => 'Bid submitted'],
            ['name' => 'TQ received'],
            ['name' => 'TQ replied'],
            ['name' => 'Disqualified'],
            ['name' => 'RA Scheduled'],
            ['name' => 'Lost'],
            ['name' => 'Won'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
