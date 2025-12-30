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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('item_name');
            $table->enum('item_type', ['Product', 'Service']);
            $table->enum('stock_type', ['Stock', 'Service']);
            $table->string('brand')->nullable();
            $table->string('oem_part_no')->unique();
            $table->string('duplicate_part_no')->nullable();
            $table->string('unit', 10);
            $table->decimal('sale_price', 10, 2);
            $table->decimal('min_sale_price', 10, 2);
            $table->boolean('vat_applicable')->default(true);
            $table->decimal('vat_rate', 5, 2)->default(5.00);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
