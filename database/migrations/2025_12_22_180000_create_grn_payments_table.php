<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grn_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grn_id');
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('payment_type');
            $table->string('paid_status')->nullable();
            $table->string('cheque_no')->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('payment_note')->nullable();
            $table->timestamps();

            $table->foreign('grn_id')->references('id')->on('grns')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grn_payments');
    }
};
