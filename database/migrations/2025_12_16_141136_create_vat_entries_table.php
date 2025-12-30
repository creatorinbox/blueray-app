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
        Schema::create('vat_entries', function (Blueprint $table) {
            $table->id();
            $table->string('reference_type');
            $table->unsignedBigInteger('reference_id');
            $table->enum('vat_type', ['Input', 'Output']);
            $table->decimal('vat_amount', 12, 2);
            $table->decimal('taxable_amount', 12, 2);
            $table->decimal('vat_rate', 5, 2);
            $table->date('vat_date');
            $table->string('trn', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vat_entries');
    }
};
