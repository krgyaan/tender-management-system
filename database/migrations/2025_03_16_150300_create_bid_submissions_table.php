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
        Schema::create('bid_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tender_id');
            $table->date('bid_submissions_date');
            $table->text('submitted_bid_documents');
            $table->string('proof_of_submission');
            $table->decimal('final_bidding_price', 15, 2);
            $table->text('reason_for_missing')->nullable();
            $table->text('not_repeat_reason')->nullable();
            $table->text('tms_improvements')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bid_submissions');
    }
};
