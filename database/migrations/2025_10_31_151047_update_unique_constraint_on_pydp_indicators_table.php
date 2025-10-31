<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the old unique constraint on title only
        Schema::table('pydp_indicators', function (Blueprint $table) {
            // Drop existing unique index if it exists
            try {
                $table->dropUnique('pydp_indicators_title_unique');
            } catch (\Exception $e) {
                // Index might not exist, continue
            }
        });

        // Add composite unique constraint (pydp_level_id + title)
        // This allows same title for different levels
        Schema::table('pydp_indicators', function (Blueprint $table) {
            $table->unique(['pydp_level_id', 'title'], 'pydp_indicators_level_title_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pydp_indicators', function (Blueprint $table) {
            // Remove composite unique constraint
            try {
                $table->dropUnique('pydp_indicators_level_title_unique');
            } catch (\Exception $e) {
                // Continue
            }

            // Restore old unique constraint on title only (optional)
            // $table->unique('title', 'pydp_indicators_title_unique');
        });
    }
};
