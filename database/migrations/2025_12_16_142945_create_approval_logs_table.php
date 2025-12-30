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
        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id();
            $table->string('module_name');
            $table->unsignedBigInteger('record_id');
            $table->enum('action', ['Submitted', 'Approved', 'Rejected', 'Revised']);
            $table->unsignedBigInteger('action_by');
            $table->timestamp('action_date');
            $table->text('remarks')->nullable();
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            
            $table->foreign('action_by')->references('id')->on('users');
            $table->index(['module_name', 'record_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_logs');
    }
};
