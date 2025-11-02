<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pydp_dataset_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pydp_indicator_id')->constrained('pydp_indicators')->onDelete('cascade');
            $table->year('year');
            $table->string('baseline')->nullable();
            $table->string('physical_target_male')->nullable();
            $table->string('physical_target_female')->nullable();
            $table->string('physical_target_total')->nullable();
            $table->string('physical_actual_male')->nullable();
            $table->string('physical_actual_female')->nullable();
            $table->string('physical_actual_total')->nullable();
            $table->string('financial_allotted')->nullable();
            $table->string('financial_spent')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pydp_dataset_entries');
    }
};