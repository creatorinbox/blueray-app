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
        // Add approval fields to transactional tables
        $tables = ['quotations', 'sales_invoices', 'purchase_orders', 'grns', 'purchase_invoices', 
                  'sales_returns', 'purchase_returns', 'job_cards', 'amc_contracts', 'expenses'];
        
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->enum('approval_status', ['Draft', 'Submitted', 'Approved', 'Rejected'])
                      ->default('Draft');
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                
                $table->foreign('approved_by')->references('id')->on('users');
                $table->foreign('created_by')->references('id')->on('users');
                $table->foreign('updated_by')->references('id')->on('users');
            });
        }
        
        // Add role to users table
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable();
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['quotations', 'sales_invoices', 'purchase_orders', 'grns', 'purchase_invoices', 
                  'sales_returns', 'purchase_returns', 'job_cards', 'amc_contracts', 'expenses'];
        
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['approved_by']);
                $table->dropForeign(['created_by']);
                $table->dropForeign(['updated_by']);
                $table->dropColumn(['approval_status', 'approved_by', 'approved_at', 'rejection_reason', 'created_by', 'updated_by']);
            });
        }
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
