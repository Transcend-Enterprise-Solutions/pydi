<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('user_data')) {
            Schema::create('user_data', function (Blueprint $table) {
                $table->id('id');
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->string('last_name');
                $table->string('first_name');
                $table->string('middle_name')->nullable();
                $table->string('name_extension')->nullable();
                $table->string('tel_number')->nullable();
                $table->string('mobile_number')->nullable();
                $table->string('block')->nullable();
                $table->string('lot')->nullable();
                $table->string('street')->nullable();
                $table->timestamps();
            });

            // Insert user_data
            DB::table('user_data')->insert([
                [
                    'id' => 1,
                    'user_id' => 1,
                    'last_name' => 'Kelley',
                    'first_name' => 'Yvette',
                    'middle_name' => 'Jakeem Brooks',
                    'name_extension' => 'Jr.',
                    'tel_number' => null,
                    'mobile_number' => '09123456789',
                    'block' => '10',
                    'lot' => '13',
                    'street' => 'Blackthorn',
                    'created_at' => '2025-07-02 13:45:48',
                    'updated_at' => '2025-07-02 13:45:48',
                ],
                [
                    'id' => 2,
                    'user_id' => 2,
                    'last_name' => 'Horn',
                    'first_name' => 'Kalia',
                    'middle_name' => 'Calvin Harris',
                    'name_extension' => 'Sr.',
                    'tel_number' => null,
                    'mobile_number' => '09123456789',
                    'block' => null,
                    'lot' => null,
                    'street' => null,
                    'created_at' => '2025-07-11 02:28:55',
                    'updated_at' => '2025-07-11 02:28:55',
                ],
                [
                    'id' => 3,
                    'user_id' => 3,
                    'last_name' => 'joyosa',
                    'first_name' => 'alvin dale',
                    'middle_name' => null,
                    'name_extension' => null,
                    'tel_number' => null,
                    'mobile_number' => '091591533314',
                    'block' => null,
                    'lot' => null,
                    'street' => null,
                    'created_at' => '2025-07-11 02:59:34',
                    'updated_at' => '2025-07-11 02:59:34',
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_data');
    }
};
