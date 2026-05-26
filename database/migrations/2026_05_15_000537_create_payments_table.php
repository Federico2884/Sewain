<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('amount', 12, 2);
            $table->decimal('rent_amount', 12, 2);
            $table->decimal('deposit_amount', 12, 2);
            $table->string('status')->default('pending');
            $table->string('method')->nullable();
            $table->string('payment_proof')->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
