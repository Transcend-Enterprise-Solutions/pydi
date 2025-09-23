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
        Schema::table('pydi_dataset_details', function (Blueprint $table) {
            $table->string('dimension_others_text')->nullable()->after('dimension_id');
            $table->string('indicator_others_text')->nullable()->after('indicator_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pydi_dataset_details', function (Blueprint $table) {
            $table->dropColumn(['dimension_others_text', 'indicator_others_text']);
        });
    }
};
