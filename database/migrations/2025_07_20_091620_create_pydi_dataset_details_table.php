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
        Schema::create('pydi_dataset_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pydi_dataset_id')
                ->constrained('pydi_datasets')
                ->cascadeOnDelete();

            $table->foreignId('dimension_id')
                ->constrained('dimensions')
                ->cascadeOnDelete();

            $table->foreignId('indicator_id')
                ->constrained('indicators')
                ->cascadeOnDelete();

            $table->foreignId('philippine_region_id')
                ->constrained('philippine_regions')
                ->cascadeOnDelete();

            $table->string('sex');
            $table->string('age');
            $table->integer('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pydi_dataset_details');
    }
};
