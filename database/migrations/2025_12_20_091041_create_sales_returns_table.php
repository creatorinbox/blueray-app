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
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_invoice_id')->constrained('sales_invoices')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('return_code')->unique();
            $table->date('return_date');
            $table->enum('return_status', ['Return', 'Cancel'])->default('Return');
            $table->string('reference_no')->nullable();
            $table->decimal('subtotal', 12, 3)->default(0);
            $table->decimal('discount_to_all_input', 12, 3)->default(0);
            $table->string('discount_to_all_type')->default('in_fixed');
            $table->decimal('other_charges_input', 12, 3)->default(0);
            $table->decimal('total_amount', 12, 3)->default(0);
            $table->text('return_note')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('sales_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_return_id')->constrained('sales_returns')->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->decimal('qty', 10, 2);
            $table->decimal('price', 10, 3);
            $table->decimal('amount', 12, 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_return_items');
        Schema::dropIfExists('sales_returns');
    }
};
