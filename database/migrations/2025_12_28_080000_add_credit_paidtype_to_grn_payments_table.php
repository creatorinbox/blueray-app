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
        Schema::table('grn_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('grn_payments', 'paid_type')) {
                $table->string('paid_type')->nullable()->after('payment_type');
            }
            if (!Schema::hasColumn('grn_payments', 'credit_due')) {
                $table->decimal('credit_due', 12, 3)->nullable()->after('paid_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grn_payments', function (Blueprint $table) {
            if (Schema::hasColumn('grn_payments', 'credit_due')) {
                $table->dropColumn('credit_due');
            }
            if (Schema::hasColumn('grn_payments', 'paid_type')) {
                $table->dropColumn('paid_type');
            }
        });
    }
};
