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
            // Add columns only if they do not already exist
            if (!Schema::hasColumn('job_cards', 'job_description')) {
                $table->text('job_description')->nullable()->after('job_card_no');
            }
            if (!Schema::hasColumn('job_cards', 'priority')) {
                $table->enum('priority', ['Low', 'Medium', 'High', 'Urgent'])->default('Medium')->after('job_description');
            }
            if (!Schema::hasColumn('job_cards', 'scheduled_date')) {
                $table->date('scheduled_date')->nullable()->after('priority');
            }
            if (!Schema::hasColumn('job_cards', 'completion_date')) {
                $table->date('completion_date')->nullable()->after('scheduled_date');
            }
            if (!Schema::hasColumn('job_cards', 'estimated_hours')) {
                $table->decimal('estimated_hours', 5, 2)->nullable()->after('completion_date');
            }
            if (!Schema::hasColumn('job_cards', 'actual_hours')) {
                $table->decimal('actual_hours', 5, 2)->nullable()->after('estimated_hours');
            }
            if (!Schema::hasColumn('job_cards', 'notes')) {
                $table->text('notes')->nullable()->after('actual_hours');
            }
            if (!Schema::hasColumn('job_cards', 'company_id')) {
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade')->after('id');
            }

            // Modify existing columns to match our needs
            if (Schema::hasColumn('job_cards', 'status')) {
                $table->enum('status', ['Pending', 'In Progress', 'Completed', 'Cancelled'])->default('Pending')->change();
            }

            // Drop columns we don't need for now (keep problem_description as job_description)
            if (Schema::hasColumn('job_cards', 'amc_id')) {
                $table->dropForeign(['amc_id']);
                $table->dropColumn(['amc_id']);
            }
            foreach (['job_type', 'job_date', 'technician', 'work_done', 'labour_charges', 'total_amount'] as $col) {
                if (Schema::hasColumn('job_cards', $col)) {
                    $table->dropColumn([$col]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_cards', function (Blueprint $table) {
            // Restore original columns
            $table->foreignId('amc_id')->nullable()->constrained('amc_contracts')->onDelete('set null');
            $table->enum('job_type', ['Paid', 'Warranty', 'AMC']);
            $table->date('job_date');
            $table->string('technician');
            $table->text('work_done')->nullable();
            $table->decimal('labour_charges', 10, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            
            // Drop added columns
            $table->dropForeign(['company_id']);
            $table->dropColumn([
                'job_description', 'priority', 'scheduled_date', 'completion_date',
                'estimated_hours', 'actual_hours', 'notes', 'company_id'
            ]);
            
            // Restore original status enum
            $table->enum('status', ['Open', 'In Progress', 'Completed', 'Closed', 'Cancelled'])->default('Open')->change();
        });
    }
};
