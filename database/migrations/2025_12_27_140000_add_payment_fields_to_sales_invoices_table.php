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
            if (!Schema::hasColumn('sales_invoices', 'payment_type')) {
                $table->string('payment_type')->nullable()->after('credit_due');
            }
            if (!Schema::hasColumn('sales_invoices', 'paid_type')) {
                $table->string('paid_type')->nullable()->after('payment_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('sales_invoices', 'payment_type')) {
                $table->dropColumn('payment_type');
            }
            if (Schema::hasColumn('sales_invoices', 'paid_type')) {
                $table->dropColumn('paid_type');
            }
        });
    }
};
