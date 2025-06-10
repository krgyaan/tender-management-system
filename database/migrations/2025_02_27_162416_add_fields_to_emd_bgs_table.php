<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('emd_bgs', function (Blueprint $table) {
            $table->string('bg_purpose')->nullable()->after('bg_needs');
            $table->date('bg_claim')->nullable()->after('bg_expiry');
            $table->string('bg_format_te')->nullable()->after('bg_courier_addr');
            $table->dateTime('bg_courier_deadline')->nullable()->after('bg_courier_addr');
        });
    }

    public function down()
    {
        Schema::table('emd_bgs', function (Blueprint $table) {
            $table->dropColumn([
                'bg_purpose',
                'bg_claim',
                'bg_format_te',
                'bg_courier_deadline'
            ]);
        });
    }
};
