<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
            }
        });

        // Set a default company_id for existing records (using the first company)
        $firstCompany = DB::table('companies')->first();
        if ($firstCompany) {
            DB::table('purchase_orders')->whereNull('company_id')->update(['company_id' => $firstCompany->id]);
        }

        // Now add the foreign key constraint and make the column not nullable
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->unsignedBigInteger('company_id')->nullable(false)->change();
        });
    }
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_orders', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }
        });
    }
};
