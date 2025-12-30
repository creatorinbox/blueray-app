<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'purchase_price')) {
                $table->decimal('purchase_price', 14, 3)->default(0)->after('sale_price');
            }
            if (!Schema::hasColumn('items', 'currency')) {
                $table->string('currency', 3)->default('OMR')->after('purchase_price');
            }
            if (!Schema::hasColumn('items', 'currency_value')) {
                $table->decimal('currency_value', 14, 3)->default(1.000)->after('currency');
            }
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'currency_value')) {
                $table->dropColumn('currency_value');
            }
            if (Schema::hasColumn('items', 'currency')) {
                $table->dropColumn('currency');
            }
            if (Schema::hasColumn('items', 'purchase_price')) {
                $table->dropColumn('purchase_price');
            }
        });
    }
};
