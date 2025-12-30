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
            // Add missing columns only if they do not already exist
            if (!Schema::hasColumn('sales_returns', 'sales_invoice_id')) {
                $table->foreignId('sales_invoice_id')->after('id')->constrained('sales_invoices')->onDelete('cascade');
            }
            if (!Schema::hasColumn('sales_returns', 'company_id')) {
                $table->foreignId('company_id')->after('customer_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('sales_returns', 'return_code')) {
                $table->string('return_code')->after('company_id')->unique();
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
            if (!Schema::hasColumn('sales_returns', 'created_by')) {
                $table->foreignId('created_by')->after('total_amount')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('sales_returns', 'return_note')) {
                $table->text('return_note')->after('created_by')->nullable();
            }

            // Drop old columns that are different
            if (Schema::hasColumn('sales_returns', 'return_no')) {
                $table->dropColumn(['return_no']);
            }
            if (Schema::hasColumn('sales_returns', 'reason')) {
                $table->dropColumn(['reason']);
            }

            // Modify existing columns
            if (Schema::hasColumn('sales_returns', 'total_amount')) {
                $table->decimal('total_amount', 12, 3)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_returns', function (Blueprint $table) {
            // Add back old columns
            $table->string('return_no')->unique();
            $table->text('reason')->nullable();
            
            // Drop new columns
            $table->dropConstrainedForeignId('sales_invoice_id');
            $table->dropConstrainedForeignId('company_id');
            $table->dropColumn([
                'return_code', 'return_status', 'reference_no',
                'subtotal', 'discount_to_all_input', 'discount_to_all_type',
                'other_charges_input', 'return_note'
            ]);
            $table->dropConstrainedForeignId('created_by');
            
            // Revert total_amount
            $table->decimal('total_amount', 12, 2)->change();
        });
    }
};
