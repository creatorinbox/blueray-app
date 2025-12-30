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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->enum('reference_type', ['Sales', 'Purchase']);
            $table->unsignedBigInteger('reference_id');
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->enum('mode', ['Cash', 'Bank', 'Card', 'Cheque', 'Online']);
            $table->string('transaction_ref')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
