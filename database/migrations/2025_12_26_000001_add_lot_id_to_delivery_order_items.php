<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('delivery_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_order_items', 'lot_id')) {
                $table->unsignedBigInteger('lot_id')->nullable()->after('item_id');
            }
        });
    }

    public function down() {
        Schema::table('delivery_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_order_items', 'lot_id')) {
                $table->dropColumn('lot_id');
            }
        });
    }
};
