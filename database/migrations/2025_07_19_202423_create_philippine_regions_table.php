<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('philippine_regions', function (Blueprint $table) {
            $table->id();
            $table->string('psgc_code', 20)->unique();
            $table->string('region_description');
            $table->string('region_code', 5)->unique();
            $table->timestamps();
        });

        DB::table('philippine_regions')->insert([
            ['id' => 1, 'psgc_code' => '010000000', 'region_description' => 'R1 - Ilocos', 'region_code' => '01', 'created_at' => '2025-07-11 02:37:12', 'updated_at' => '2025-07-27 07:01:51'],
            ['id' => 2, 'psgc_code' => '020000000', 'region_description' => 'R2 - Cagayan', 'region_code' => '02', 'created_at' => '2025-07-11 02:37:12', 'updated_at' => '2025-07-27 07:01:56'],
            ['id' => 3, 'psgc_code' => '030000000', 'region_description' => 'R3 - Central Luzon', 'region_code' => '03', 'created_at' => '2025-07-11 02:37:12', 'updated_at' => '2025-07-27 07:02:21'],
            ['id' => 4, 'psgc_code' => '040000000', 'region_description' => 'R4A - Calabarzon', 'region_code' => '04', 'created_at' => '2025-07-11 02:37:12', 'updated_at' => '2025-07-27 07:02:27'],
            ['id' => 5, 'psgc_code' => '170000000', 'region_description' => 'R4B - Mimaropa', 'region_code' => '17', 'created_at' => '2025-07-11 02:37:12', 'updated_at' => '2025-07-27 07:02:32'],
            ['id' => 6, 'psgc_code' => '050000000', 'region_description' => 'R5 - Bicol', 'region_code' => '05', 'created_at' => '2025-07-11 02:37:12', 'updated_at' => '2025-07-27 07:02:39'],
            ['id' => 7, 'psgc_code' => '060000000', 'region_description' => 'R6 - W. Visayas', 'region_code' => '06', 'created_at' => '2025-07-11 02:37:12', 'updated_at' => '2025-07-27 07:02:50'],
            ['id' => 8, 'psgc_code' => '070000000', 'region_description' => 'R7 - C. Visayas', 'region_code' => '07', 'created_at' => '2025-07-11 02:37:12', 'updated_at' => '2025-07-27 07:03:06'],
            ['id' => 9, 'psgc_code' => '080000000', 'region_description' => 'R8 - E. Visayas', 'region_code' => '08', 'created_at' => '2025-07-11 02:37:12', 'updated_at' => '2025-07-27 07:03:12'],
            ['id' => 10, 'psgc_code' => '090000000', 'region_description' => 'R9 - Zamboanga', 'region_code' => '09', 'created_at' => '2025-07-11 02:37:12', 'updated_at' => '2025-07-27 07:03:43'],
            ['id' => 11, 'psgc_code' => '100000000', 'region_description' => 'R10 - N. Mindanao', 'region_code' => '10', 'created_at' => '2025-07-11 02:37:12', 'updated_at' => '2025-07-27 07:03:49'],
            ['id' => 12, 'psgc_code' => '110000000', 'region_description' => 'R11 - Davao', 'region_code' => '11', 'created_at' => '2025-07-11 02:37:12', 'updated_at' => '2025-07-27 07:03:53'],
            ['id' => 13, 'psgc_code' => '120000000', 'region_description' => 'R12 - Soccsksargen', 'region_code' => '12', 'created_at' => '2025-07-11 02:37:12', 'updated_at' => '2025-07-27 07:03:58'],
            ['id' => 14, 'psgc_code' => '130000000', 'region_description' => 'NCR', 'region_code' => '13', 'created_at' => '2025-07-11 02:37:12', 'updated_at' => '2025-07-27 07:04:02'],
            ['id' => 15, 'psgc_code' => '140000000', 'region_description' => 'CAR', 'region_code' => '14', 'created_at' => '2025-07-11 02:37:12', 'updated_at' => '2025-07-27 07:04:05'],
            ['id' => 16, 'psgc_code' => '150000000', 'region_description' => 'BARMM', 'region_code' => '15', 'created_at' => '2025-07-11 02:37:12', 'updated_at' => '2025-07-27 07:04:11'],
            ['id' => 17, 'psgc_code' => '160000000', 'region_description' => 'R13', 'region_code' => '16', 'created_at' => '2025-07-11 02:37:12', 'updated_at' => '2025-07-27 07:04:14'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('philippine_regions');
    }
};
