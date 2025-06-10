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
        Schema::create('submit_queries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tender_id')->unsigned();
            $table->string('tender_no');
            $table->string('client_org');
            $table->string('client_name');
            $table->string('client_email');
            $table->string('client_phone');
            $table->timestamps();
        });

        Schema::create('submit_queries_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submit_queries_id')->constrained('submit_queries')->onDelete('cascade');
            $table->string('page_no');
            $table->string('clause_no');
            $table->enum('query_type', ['technical', 'commercial', 'bec', 'price_bid']);
            $table->text('current_statement');
            $table->text('requested_statement');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('submit_queries');
        Schema::dropIfExists('submit_queries_lists');
    }
};
