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
            // Check if old columns exist and drop them
            if (Schema::hasColumn('sales_returns', 'return_no')) {
                $table->dropColumn('return_no');
            }
            if (Schema::hasColumn('sales_returns', 'reason')) {
                $table->dropColumn('reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_returns', function (Blueprint $table) {
            $table->string('return_no')->unique()->nullable();
            $table->text('reason')->nullable();
        });
    }
};
