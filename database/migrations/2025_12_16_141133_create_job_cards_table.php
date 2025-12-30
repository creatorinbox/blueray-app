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
        Schema::create('job_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('amc_id')->nullable()->constrained('amc_contracts')->onDelete('set null');
            $table->enum('job_type', ['Paid', 'Warranty', 'AMC']);
            $table->string('job_card_no')->unique();
            $table->date('job_date');
            $table->string('technician');
            $table->enum('status', ['Open', 'In Progress', 'Completed', 'Closed', 'Cancelled'])->default('Open');
            $table->text('problem_description');
            $table->text('work_done')->nullable();
            $table->decimal('labour_charges', 10, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_cards');
    }
};
