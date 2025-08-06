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
        Schema::create('pydp_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pydp_dataset_detail_id')->nullable()->constrained('pydp_dataset_details')->onDelete('set null');
            $table->year('year');
            $table->decimal('target_physical', 10, 2)->default(0.00);
            $table->decimal('target_financial', 10, 2)->default(0.00);
            $table->decimal('actual_physical', 10, 2)->default(0.00);
            $table->decimal('actual_financial', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pydp_years');
    }
};
