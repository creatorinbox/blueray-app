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
            $table->enum('delivery_status', ['Pending', 'Approved', 'Delivered'])->default('Pending')->after('invoice_status');
            $table->timestamp('delivery_approved_at')->nullable()->after('delivery_status');
            $table->foreignId('delivery_approved_by')->nullable()->after('delivery_approved_at')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropForeign(['delivery_approved_by']);
            $table->dropColumn(['delivery_status', 'delivery_approved_at', 'delivery_approved_by']);
        });
    }
};
