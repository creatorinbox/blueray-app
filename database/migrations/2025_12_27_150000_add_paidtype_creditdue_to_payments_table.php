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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'paid_type')) {
                $table->string('paid_type')->nullable()->after('mode');
            }
            if (!Schema::hasColumn('payments', 'credit_due')) {
                $table->decimal('credit_due', 12, 3)->nullable()->after('paid_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'credit_due')) {
                $table->dropColumn('credit_due');
            }
            if (Schema::hasColumn('payments', 'paid_type')) {
                $table->dropColumn('paid_type');
            }
        });
    }
};
