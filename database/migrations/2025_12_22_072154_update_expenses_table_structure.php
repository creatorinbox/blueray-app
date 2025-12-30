<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Add new columns needed for expense management
            $table->unsignedBigInteger('category_id')->nullable()->after('expense_date');
            $table->unsignedBigInteger('sub_category_id')->nullable()->after('category_id');
            $table->unsignedBigInteger('customer_id')->nullable()->after('sub_category_id');
            $table->string('expense_for')->nullable()->after('customer_id');
            $table->decimal('expense_amount', 15, 3)->default(0)->after('expense_for');
            $table->string('vat_type')->default('withoutvat')->after('expense_amount');
            $table->decimal('total_amount', 15, 3)->default(0)->after('vat_type');
            $table->string('vehicle_no')->nullable()->after('total_amount');
            $table->text('note')->nullable()->after('vehicle_no');
            
            // Add foreign key constraints
            $table->foreign('category_id')->references('id')->on('expense_categories');
            $table->foreign('sub_category_id')->references('id')->on('expense_sub_categories');
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['category_id']);
            $table->dropForeign(['sub_category_id']);
            $table->dropForeign(['customer_id']);
            
            // Drop the new columns
            $table->dropColumn([
                'category_id',
                'sub_category_id', 
                'customer_id',
                'expense_for',
                'expense_amount',
                'vat_type',
                'total_amount',
                'vehicle_no',
                'note'
            ]);
        });
    }
};
