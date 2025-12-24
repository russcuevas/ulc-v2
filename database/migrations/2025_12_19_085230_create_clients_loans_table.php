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
        Schema::create('clients_loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->string('pn_number')->unique();
            $table->string('release_number')->unique();
            $table->date('loan_from');
            $table->date('loan_to');
            $table->decimal('loan_amount', 12, 2);
            $table->decimal('balance', 12, 2)->nullable();
            $table->decimal('daily', 12, 2)->nullable();
            $table->decimal('principal', 12, 2);
            $table->string('loan_terms');
            $table->string('loan_status');
            $table->string('payment_status');
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients_loans');
    }
};
