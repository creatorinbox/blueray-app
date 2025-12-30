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
        Schema::table('items', function (Blueprint $table) {
            $table->string('hsn_code', 50)->nullable()->after('duplicate_part_no');
            $table->decimal('min_quantity', 10, 2)->default(0)->after('unit');
            $table->decimal('opening_stock', 10, 2)->default(0)->after('min_quantity');
            $table->string('barcode', 100)->nullable()->unique()->after('opening_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['hsn_code', 'min_quantity', 'opening_stock', 'barcode']);
        });
    }
};
