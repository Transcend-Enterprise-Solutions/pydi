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
        Schema::table('pydp_indicators', function (Blueprint $table) {
            $table->text('data_sources')->nullable()->after('content');
            $table->string('frequency')->nullable()->after('data_sources');
            $table->text('responsible')->nullable()->after('frequency');
            $table->text('validation')->nullable()->after('responsible');
            $table->text('data_sharing')->nullable()->after('validation');
            $table->string('measurement_unit')->nullable()->after('data_sharing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pydp_indicators', function (Blueprint $table) {
            $table->dropColumn([
                'data_sources',
                'frequency',
                'responsible',
                'validation',
                'data_sharing',
                'measurement_unit'
            ]);
        });
    }
};