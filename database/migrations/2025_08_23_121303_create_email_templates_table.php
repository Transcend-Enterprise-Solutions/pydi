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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'training_invitation'
            $table->string('subject')->nullable();
            $table->string('header')->nullable();
            $table->string('greetings')->nullable();
            $table->longText('message_body')->nullable();
            $table->string('footer')->nullable();
            $table->string('action_button_text')->nullable();
            $table->string('action_button_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
