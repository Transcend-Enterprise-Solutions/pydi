<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accomplishments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicator_id')->constrained()->onDelete('cascade');
            $table->string('ppa_name'); // Programs, Projects, and Activities
            $table->year('year');
            $table->decimal('target_physical', 15, 2)->nullable();
            $table->decimal('target_financial', 15, 2)->nullable();
            $table->decimal('actual_physical', 15, 2)->nullable();
            $table->decimal('actual_financial', 15, 2)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'needs_revision'])->default('pending');
            $table->text('admin_feedback')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            // Ensure unique combination of indicator, PPA, and year
            $table->unique(['indicator_id', 'ppa_name', 'year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('accomplishments');
    }
};