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
        Schema::table('pydp_years', function (Blueprint $table) {
            $table->decimal('baseline', 10, 2)->nullable()->after('actual_financial');
            $table->decimal('total', 10, 2)->nullable()->after('baseline');
            $table->text('remarks')->nullable()->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pydp_years', function (Blueprint $table) {
            $table->dropColumn(['baseline', 'total', 'remarks']);
        });
    }
};
