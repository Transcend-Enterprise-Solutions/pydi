<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing regions with full descriptions
        DB::table('philippine_regions')->where('id', 1)->update(['region_description' => 'Region I - Ilocos Region']);
        DB::table('philippine_regions')->where('id', 2)->update(['region_description' => 'Region II - Cagayan Valley']);
        DB::table('philippine_regions')->where('id', 3)->update(['region_description' => 'Region III - Central Luzon']);
        DB::table('philippine_regions')->where('id', 4)->update(['region_description' => 'Region IV-A - CALABARZON']);
        DB::table('philippine_regions')->where('id', 5)->update(['region_description' => 'MIMAROPA Region']);
        DB::table('philippine_regions')->where('id', 6)->update(['region_description' => 'Region V - Bicol Region']);
        DB::table('philippine_regions')->where('id', 7)->update(['region_description' => 'Region VI - Western Visayas']);
        DB::table('philippine_regions')->where('id', 8)->update(['region_description' => 'Region VII - Central Visayas']);
        DB::table('philippine_regions')->where('id', 9)->update(['region_description' => 'Region VIII - Eastern Visayas']);
        DB::table('philippine_regions')->where('id', 10)->update(['region_description' => 'Region IX - Zamboanga Peninsula']);
        DB::table('philippine_regions')->where('id', 11)->update(['region_description' => 'Region X - Northern Mindanao']);
        DB::table('philippine_regions')->where('id', 12)->update(['region_description' => 'Region XI - Davao Region']);
        DB::table('philippine_regions')->where('id', 13)->update(['region_description' => 'Region XII - SOCCSKSARGEN']);
        DB::table('philippine_regions')->where('id', 14)->update(['region_description' => 'National Capital Region (NCR)']);
        DB::table('philippine_regions')->where('id', 15)->update(['region_description' => 'Cordillera Administrative Region (CAR)']);
        DB::table('philippine_regions')->where('id', 16)->update(['region_description' => 'Bangsamoro Autonomous Region in Muslim Mindanao (BARMM)']);
        DB::table('philippine_regions')->where('id', 17)->update(['region_description' => 'Region XIII - Caraga']);

        // Add Negros Island Region if it doesn't exist
        if (!DB::table('philippine_regions')->where('id', 18)->exists()) {
            DB::table('philippine_regions')->insert([
                'id' => 18,
                'psgc_code' => '180000000',
                'region_description' => 'Negros Island Region (NIR)',
                'region_code' => '18',
                'created_at' => '2025-07-11 02:37:12',
                'updated_at' => '2025-07-27 07:04:18',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old descriptions
        DB::table('philippine_regions')->where('id', 1)->update(['region_description' => 'R1 - Ilocos']);
        DB::table('philippine_regions')->where('id', 2)->update(['region_description' => 'R2 - Cagayan']);
        DB::table('philippine_regions')->where('id', 3)->update(['region_description' => 'R3 - Central Luzon']);
        DB::table('philippine_regions')->where('id', 4)->update(['region_description' => 'R4A - Calabarzon']);
        DB::table('philippine_regions')->where('id', 5)->update(['region_description' => 'R4B - Mimaropa']);
        DB::table('philippine_regions')->where('id', 6)->update(['region_description' => 'R5 - Bicol']);
        DB::table('philippine_regions')->where('id', 7)->update(['region_description' => 'R6 - W. Visayas']);
        DB::table('philippine_regions')->where('id', 8)->update(['region_description' => 'R7 - C. Visayas']);
        DB::table('philippine_regions')->where('id', 9)->update(['region_description' => 'R8 - E. Visayas']);
        DB::table('philippine_regions')->where('id', 10)->update(['region_description' => 'R9 - Zamboanga']);
        DB::table('philippine_regions')->where('id', 11)->update(['region_description' => 'R10 - N. Mindanao']);
        DB::table('philippine_regions')->where('id', 12)->update(['region_description' => 'R11 - Davao']);
        DB::table('philippine_regions')->where('id', 13)->update(['region_description' => 'R12 - Soccsksargen']);
        DB::table('philippine_regions')->where('id', 14)->update(['region_description' => 'NCR']);
        DB::table('philippine_regions')->where('id', 15)->update(['region_description' => 'CAR']);
        DB::table('philippine_regions')->where('id', 16)->update(['region_description' => 'BARMM']);
        DB::table('philippine_regions')->where('id', 17)->update(['region_description' => 'R13']);
        
        // Remove NIR if added
        DB::table('philippine_regions')->where('id', 18)->delete();
    }
};
