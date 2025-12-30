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
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->string('invoice_no');
            $table->date('invoice_date');
            $table->enum('invoice_type', ['Cash', 'Credit'])->default('Credit');
            $table->decimal('total_amount', 12, 2);
            $table->decimal('vat_amount', 12, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->enum('status', ['Pending', 'Paid', 'Partial', 'Overdue'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
