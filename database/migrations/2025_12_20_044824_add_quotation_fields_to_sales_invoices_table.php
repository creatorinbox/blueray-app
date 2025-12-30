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
        Schema::table('sales_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_invoices', 'quotation_id')) {
                $table->unsignedBigInteger('quotation_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('sales_invoices', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('customer_id');
            }
            if (!Schema::hasColumn('sales_invoices', 'reference_no')) {
                $table->string('reference_no')->nullable()->after('invoice_no');
            }
            if (!Schema::hasColumn('sales_invoices', 'invoice_status')) {
                $table->string('invoice_status')->default('final')->after('invoice_type')->comment('performance or final');
            }
            if (!Schema::hasColumn('sales_invoices', 'subtotal')) {
                $table->decimal('subtotal', 12, 3)->default(0)->after('invoice_type');
            }
            if (!Schema::hasColumn('sales_invoices', 'discount')) {
                $table->decimal('discount', 12, 3)->default(0)->after('subtotal');
            }
            if (!Schema::hasColumn('sales_invoices', 'other_charges')) {
                $table->decimal('other_charges', 12, 3)->default(0)->after('discount');
            }
            if (!Schema::hasColumn('sales_invoices', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
        });
        
        // Add foreign keys separately
        Schema::table('sales_invoices', function (Blueprint $table) {
            if (!$this->hasForeignKey('sales_invoices', 'sales_invoices_quotation_id_foreign')) {
                $table->foreign('quotation_id', 'sales_invoices_quotation_id_foreign')
                      ->references('id')->on('quotations')->onDelete('set null');
            }
            if (!$this->hasForeignKey('sales_invoices', 'sales_invoices_company_id_foreign')) {
                $table->foreign('company_id', 'sales_invoices_company_id_foreign')
                      ->references('id')->on('companies')->onDelete('cascade');
            }
        });
    }
    
    private function hasForeignKey($table, $name)
    {
        $conn = Schema::getConnection();
        $db = $conn->getDatabaseName();
        $result = $conn->select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = '{$db}' 
            AND TABLE_NAME = '{$table}' 
            AND CONSTRAINT_NAME = '{$name}'
        ");
        return !empty($result);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropForeign(['quotation_id']);
            $table->dropForeign(['company_id']);
            $table->dropColumn([
                'quotation_id',
                'company_id',
                'reference_no',
                'invoice_status',
                'subtotal',
                'discount',
                'other_charges',
                'notes'
            ]);
        });
    }
};
