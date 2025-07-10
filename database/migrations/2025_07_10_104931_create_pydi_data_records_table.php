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
        Schema::create('pydi_data_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upload_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('dimension_id')->constrained()->onDelete('cascade');
            $table->foreignId('indicator_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('region');
            $table->enum('sex', ['Male', 'Female', 'Both']);
            $table->string('age'); // Can be ranges like "15-24" or "All Ages"
            $table->decimal('value', 12, 4); // Value of the indicator
            $table->text('remarks')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['upload_session_id', 'status']);
            $table->index(['dimension_id', 'indicator_id']);
            $table->index(['user_id', 'status']);
            $table->index(['region', 'sex', 'age']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pydi_data_records');
    }
};
