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
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained();
            $table->date('delivery_date');
            $table->string('reference_no')->nullable();
            $table->string('subject')->nullable();
            $table->enum('delivery_status', ['Pending', 'Delivered', 'Cancelled'])->default('Pending');
            $table->decimal('subtotal', 15, 3)->default(0);
            $table->decimal('tax_amount', 15, 3)->default(0);
            $table->decimal('discount_amount', 15, 3)->default(0);
            $table->decimal('total_amount', 15, 3)->default(0);
            $table->text('delivery_notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_notes');
    }
};
