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
        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->foreignId('category_id')->constrained('expense_categories')->onDelete('cascade');
                $table->foreignId('sub_category_id')->nullable()->constrained('expense_sub_categories')->onDelete('set null');
                $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
                $table->date('expense_date');
                $table->string('expense_for');
                $table->decimal('expense_amount', 12, 3);
                $table->enum('vat_type', ['withoutvat', 'vat'])->default('withoutvat');
                $table->decimal('total_amount', 12, 3);
                $table->string('vehicle_no')->nullable();
                $table->string('reference_no')->nullable();
                $table->text('note')->nullable();
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
