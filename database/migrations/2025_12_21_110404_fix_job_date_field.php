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
            // Make job_date nullable to fix the immediate issue
            $table->date('job_date')->nullable()->change();
            
            // Add job_description column if it doesn't exist
            if (!Schema::hasColumn('job_cards', 'job_description')) {
                $table->text('job_description')->nullable()->after('job_card_no');
            }
            
            // Add other missing columns if they don't exist
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_cards', function (Blueprint $table) {
            $table->date('job_date')->nullable(false)->change();
        });
    }
};
