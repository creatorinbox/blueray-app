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
        Schema::table('customers', function (Blueprint $table) {
            // Add new customer fields based on reference form
            $table->string('customer_username')->nullable()->after('customer_name');
            $table->string('designation')->nullable()->after('customer_username');
            $table->string('mobile', 20)->nullable()->after('designation');
            $table->string('alt_email')->nullable()->after('email');
            $table->string('gstin', 15)->nullable()->after('alt_email');
            $table->string('tax_number', 20)->nullable()->after('gstin');
            $table->decimal('opening_balance', 12, 2)->default(0)->after('credit_limit');
            $table->string('custom_period')->nullable()->after('opening_balance');
            $table->string('country', 100)->nullable()->after('custom_period');
            $table->string('state', 100)->nullable()->after('country');
            $table->string('city', 100)->nullable()->after('state');
            $table->string('postcode', 10)->nullable()->after('city');
            $table->text('customer_notes')->nullable()->after('address');
            
            // Modify TRN to be more like tax_number (keeping for backward compatibility)
            $table->string('trn', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'customer_username',
                'designation', 
                'mobile',
                'alt_email',
                'gstin',
                'tax_number',
                'opening_balance',
                'custom_period',
                'country',
                'state', 
                'city',
                'postcode',
                'customer_notes'
            ]);
        });
    }
};
