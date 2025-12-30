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
        Schema::create('grn_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grn_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->string('lot_no');
            $table->date('expiry_date')->nullable();
            $table->decimal('qty_received', 10, 2);
            $table->decimal('base_cost', 10, 4);
            $table->decimal('duty_amount', 10, 4)->default(0);
            $table->decimal('freight_amount', 10, 4)->default(0);
            $table->decimal('landed_cost_per_unit', 10, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grn_items');
    }
};
