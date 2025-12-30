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
        Schema::table('sales_returns', function (Blueprint $table) {
            // Check if column doesn't exist before adding
            if (!Schema::hasColumn('sales_returns', 'sales_invoice_id')) {
                $table->unsignedBigInteger('sales_invoice_id')->after('id')->nullable();
            }
            if (!Schema::hasColumn('sales_returns', 'company_id')) {
                $table->unsignedBigInteger('company_id')->after('customer_id')->nullable();
            }
            if (!Schema::hasColumn('sales_returns', 'return_code')) {
                $table->string('return_code')->after('company_id')->nullable();
            }
            if (!Schema::hasColumn('sales_returns', 'return_status')) {
                $table->enum('return_status', ['Return', 'Cancel'])->after('return_date')->default('Return');
            }
            if (!Schema::hasColumn('sales_returns', 'reference_no')) {
                $table->string('reference_no')->after('return_status')->nullable();
            }
            if (!Schema::hasColumn('sales_returns', 'subtotal')) {
                $table->decimal('subtotal', 12, 3)->after('reference_no')->default(0);
            }
            if (!Schema::hasColumn('sales_returns', 'discount_to_all_input')) {
                $table->decimal('discount_to_all_input', 12, 3)->after('subtotal')->default(0);
            }
            if (!Schema::hasColumn('sales_returns', 'discount_to_all_type')) {
                $table->string('discount_to_all_type')->after('discount_to_all_input')->default('in_fixed');
            }
            if (!Schema::hasColumn('sales_returns', 'other_charges_input')) {
                $table->decimal('other_charges_input', 12, 3)->after('discount_to_all_type')->default(0);
            }
            if (!Schema::hasColumn('sales_returns', 'return_note')) {
                $table->text('return_note')->after('total_amount')->nullable();
            }
            if (!Schema::hasColumn('sales_returns', 'created_by')) {
                $table->unsignedBigInteger('created_by')->after('return_note')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_returns', function (Blueprint $table) {
            $table->dropColumn([
                'sales_invoice_id', 'company_id', 'return_code', 'return_status',
                'reference_no', 'subtotal', 'discount_to_all_input', 'discount_to_all_type',
                'other_charges_input', 'return_note', 'created_by'
            ]);
        });
    }
};
