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
        Schema::table('sales_return_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_return_items', 'price')) {
                $table->decimal('price', 10, 3)->after('qty');
            }
            
            // Update amount precision to match
            $table->decimal('amount', 12, 3)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_return_items', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->decimal('amount', 12, 2)->change();
        });
    }
};
