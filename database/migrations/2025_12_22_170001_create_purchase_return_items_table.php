<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('purchase_return_items')) {
            Schema::create('purchase_return_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_return_id')->constrained()->onDelete('cascade');
                $table->foreignId('item_id')->constrained()->onDelete('cascade');
                $table->foreignId('lot_id')->nullable()->constrained('stock_lots')->onDelete('set null');
                $table->decimal('qty', 10, 2);
                $table->decimal('rate', 10, 2);
                $table->decimal('amount', 12, 2);
                $table->text('reason')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_return_items');
    }
};
