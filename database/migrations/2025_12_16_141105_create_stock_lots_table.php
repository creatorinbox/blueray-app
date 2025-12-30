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
        Schema::create('stock_lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->string('lot_no');
            $table->date('expiry_date')->nullable();
            $table->decimal('qty_available', 10, 2)->default(0);
            $table->decimal('cost_price', 10, 4);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['item_id', 'lot_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_lots');
    }
};
