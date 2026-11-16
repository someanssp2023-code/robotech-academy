<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_histories', function (Blueprint $table) {
            // Add after quantity_added if column doesn't exist
            if (!Schema::hasColumn('stock_histories', 'adj_type')) {
                $table->string('adj_type', 20)->default('add')->after('quantity_added')
                      ->comment('add or subtract');
            }
            if (!Schema::hasColumn('stock_histories', 'reason')) {
                $table->string('reason', 50)->nullable()->after('adj_type')
                      ->comment('restock, damaged, returned, correction, expired, other');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_histories', function (Blueprint $table) {
            $table->dropColumn(['adj_type', 'reason']);
        });
    }
};