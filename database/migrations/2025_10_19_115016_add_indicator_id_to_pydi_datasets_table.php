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
        Schema::table('pydi_datasets', function (Blueprint $table) {
            $table->foreignId('indicator_id')->nullable()->after('user_id')->constrained('indicators')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pydi_datasets', function (Blueprint $table) {
            $table->dropForeign(['indicator_id']);
            $table->dropColumn('indicator_id');
        });
    }
};
