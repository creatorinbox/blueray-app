<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'currency_value')) {
                $table->decimal('currency_value', 14, 3)->default(1.000)->after('currency');
            }
            if (!Schema::hasColumn('purchase_orders', 'total_amount_omr')) {
                $table->decimal('total_amount_omr', 14, 3)->nullable()->after('total_amount');
            }
        });
    }

    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_orders', 'total_amount_omr')) {
                $table->dropColumn('total_amount_omr');
            }
            if (Schema::hasColumn('purchase_orders', 'currency_value')) {
                $table->dropColumn('currency_value');
            }
        });
    }
};
