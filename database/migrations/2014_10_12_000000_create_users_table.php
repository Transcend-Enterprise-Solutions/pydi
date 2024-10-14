<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void{
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id('id');
                $table->string('name');
                $table->string('email', 50);
                $table->string('password', 1000);
                $table->string('user_role')->nullable();
                $table->string('position_id')->nullable();
                $table->string('committee_id')->nullable();
                $table->string('active_status')->nullable();
                $table->string('profile_photo_path')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }
    }

    public function down(): void{
        if (Schema::hasTable('users')) {
            Schema::dropIfExists('users');
        }
    }
};
