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
        Schema::table('pydp_dataset_details', function (Blueprint $table) {
            // Remove these columns since they are now per year in pydp_years table
            $table->dropColumn(['baseline', 'total', 'remarks']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pydp_dataset_details', function (Blueprint $table) {
            // Add back the columns in case of rollback
            $table->decimal('baseline', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->text('remarks')->nullable();
        });
    }
};
