<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('purchase_returns')) {
            Schema::create('purchase_returns', function (Blueprint $table) {
                $table->id();
                $table->string('return_no')->unique();
                $table->date('date');
                $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
                $table->foreignId('grn_id')->nullable()->constrained('grns')->onDelete('set null');
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->text('reason')->nullable();
                $table->decimal('vat_reversal', 12, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};
