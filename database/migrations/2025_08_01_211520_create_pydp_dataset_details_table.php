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
        Schema::create('pydp_dataset_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pydp_dataset_id')->nullable()->constrained('pydp_datasets')->onDelete('set null');
            $table->foreignId('dimension_id')->nullable()->constrained('dimensions')->onDelete('set null');
            $table->foreignId('pydp_indicator_id')->nullable()->constrained('pydp_indicators')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pydp_dataset_details');
    }
};
