<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('amc_services', function (Blueprint $table) {
            $table->id();
            $table->string('amc_no')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('service_item');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('amc_type', ['Labour', 'Comprehensive']);
            $table->decimal('contract_value', 12, 2);
            $table->decimal('vat', 12, 2)->default(0);
            $table->string('invoice_ref')->nullable();
            $table->enum('status', ['Active', 'Expired'])->default('Active');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('amc_services');
    }
};
