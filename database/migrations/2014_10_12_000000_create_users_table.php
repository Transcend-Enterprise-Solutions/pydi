<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id('id');
                $table->string('name');
                $table->string('email', 50)->unique();
                $table->string('password', 1000);
                $table->string('user_role')->nullable();
                $table->string('active_status')->nullable();
                $table->string('profile_photo_path')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });

            // Insert sample users
            DB::table('users')->insert([
                [
                    'id' => 1,
                    'name' => 'Yvette J. Kelley Jr.',
                    'email' => 'sa@gmail.com',
                    'password' => '$2y$10$DR/MdVGsWuTbe9xtDGLDH.U/SfPgt7HX3PAAFusMoVecayj3t0yEy',
                    'user_role' => 'sa',
                    'active_status' => '0',
                    'created_at' => '2025-07-02 13:45:48',
                    'updated_at' => '2025-07-02 13:45:48',
                ],
                [
                    'id' => 2,
                    'name' => 'Kalia C. Horn Sr.',
                    'email' => 'emp@gmail.com',
                    'password' => '$2y$10$DR/MdVGsWuTbe9xtDGLDH.U/SfPgt7HX3PAAFusMoVecayj3t0yEy',
                    'user_role' => 'user',
                    'active_status' => '1',
                    'created_at' => '2025-07-11 02:28:55',
                    'updated_at' => '2025-07-11 02:29:58',
                ],
                [
                    'id' => 3,
                    'name' => 'alvin dale joyosa',
                    'email' => 'test@gmail.com',
                    'password' => '$2y$10$DR/MdVGsWuTbe9xtDGLDH.U/SfPgt7HX3PAAFusMoVecayj3t0yEy',
                    'user_role' => 'user',
                    'active_status' => '1',
                    'created_at' => '2025-07-11 02:59:34',
                    'updated_at' => '2025-07-11 03:00:00',
                ],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
