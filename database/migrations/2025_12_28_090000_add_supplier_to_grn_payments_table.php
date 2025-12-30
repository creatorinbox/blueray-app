<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('grn_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('grn_payments', 'supplier_id')) {
                $table->unsignedBigInteger('supplier_id')->nullable()->after('grn_id');
                $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('grn_payments', function (Blueprint $table) {
            if (Schema::hasColumn('grn_payments', 'supplier_id')) {
                $table->dropForeign(['supplier_id']);
                $table->dropColumn('supplier_id');
            }
        });
    }
};
