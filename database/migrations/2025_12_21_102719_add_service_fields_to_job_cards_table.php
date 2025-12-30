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
        Schema::table('job_cards', function (Blueprint $table) {
            $table->string('invoice_no', 100)->nullable()->after('job_card_no');
            $table->string('model_no', 100)->nullable()->after('invoice_no');
            $table->string('serial_no', 100)->nullable()->after('model_no');
            $table->string('service_attend', 100)->nullable()->after('serial_no');
            $table->string('service_attend_mobile', 20)->nullable()->after('service_attend');
            $table->string('loading_hr', 50)->nullable()->after('service_attend_mobile');
            $table->time('service_start_time')->nullable()->after('loading_hr');
            $table->time('service_end_time')->nullable()->after('service_start_time');
            $table->string('reference_no', 100)->nullable()->after('service_end_time');
            $table->date('job_report_date')->nullable()->after('reference_no');
            $table->string('job_report_no', 100)->nullable()->after('job_report_date');
            $table->text('service_remarks')->nullable()->after('job_report_no');
            $table->text('customer_remarks')->nullable()->after('service_remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_cards', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_no', 
                'model_no', 
                'serial_no', 
                'service_attend', 
                'service_attend_mobile', 
                'loading_hr', 
                'service_start_time', 
                'service_end_time', 
                'reference_no', 
                'job_report_date', 
                'job_report_no', 
                'service_remarks', 
                'customer_remarks'
            ]);
        });
    }
};
