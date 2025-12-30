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
            // Update status enum to include 'Pending'
            $table->enum('status', ['Pending', 'Open', 'In Progress', 'Completed', 'Closed', 'Cancelled'])->default('Pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_cards', function (Blueprint $table) {
            // Revert to original enum values
            $table->enum('status', ['Open', 'In Progress', 'Completed', 'Closed', 'Cancelled'])->default('Open')->change();
        });
    }
};
