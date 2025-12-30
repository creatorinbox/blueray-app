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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('lot_id')->constrained('stock_lots')->onDelete('cascade');
            $table->enum('movement_type', ['IN', 'OUT']);
            $table->enum('reference_type', ['GRN', 'Sale', 'JobCard', 'Damage', 'Return', 'Adjustment']);
            $table->unsignedBigInteger('reference_id');
            $table->decimal('qty', 10, 2);
            $table->date('movement_date');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
