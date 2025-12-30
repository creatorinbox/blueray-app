<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        $tables = [
            'quotation_items',
            'purchase_order_items',
            'grn_items',
            'sales_invoice_items',
            'sales_return_items',
            'purchase_return_items',
            'delivery_note_items'
        ];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                if (!Schema::hasColumn($table->getTable(), 'lot_id')) {
                    $table->unsignedBigInteger('lot_id')->nullable()->after('item_id');
                }
            });
        }
    }
    public function down() {
        $tables = [
            'quotation_items',
            'purchase_order_items',
            'grn_items',
            'sales_invoice_items',
            'sales_return_items',
            'purchase_return_items',
            'delivery_note_items'
        ];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'lot_id')) {
                    $table->dropColumn('lot_id');
                }
            });
        }
    }
};
