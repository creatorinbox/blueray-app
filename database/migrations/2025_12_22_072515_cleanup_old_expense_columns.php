<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Make old columns nullable to avoid conflicts
            $table->string('category')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->decimal('amount', 15, 3)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Revert changes if needed
            $table->string('category')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
            $table->decimal('amount', 15, 3)->nullable(false)->change();
        });
    }
};
