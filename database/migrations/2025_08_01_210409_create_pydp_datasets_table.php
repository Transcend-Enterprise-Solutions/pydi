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
        Schema::create('pydp_datasets', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('pydp_type_id')->nullable()->constrained('pydp_types')->onDelete('set null');
            $table->foreignId('pydp_level_id')->nullable()->constrained('pydp_levels')->onDelete('set null');

            // Metadata
            $table->string('name');
            $table->text('description');

            // Status and workflow
            $table->enum('status', ['pending', 'approved', 'rejected', 'needs_revision'])->default('pending');
            $table->boolean('is_submitted')->default(false);
            $table->boolean('is_request_edit')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->text('feedback')->nullable();

            // File
            $table->string('file_path')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pydp_datasets');
    }
};
