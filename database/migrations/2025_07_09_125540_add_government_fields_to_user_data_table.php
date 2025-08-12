<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('user_data', function (Blueprint $table) {
            $table->string('position_designation')->nullable()->after('mobile_number');
            $table->string('government_agency')->nullable()->after('position_designation');
            $table->string('office_department_division')->nullable()->after('government_agency');
            $table->text('office_address')->nullable()->after('office_department_division');

            // Add indexes for better performance on searchable fields
            $table->index('position_designation');
            $table->index('government_agency');
            $table->index('office_department_division');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('user_data', function (Blueprint $table) {
            $table->dropColumn([
                'position_designation',
                'government_agency',
                'office_department_division',
                'office_address'
            ]);

            // Drop the indexes if they exist
            $table->dropIndex(['position_designation']);
            $table->dropIndex(['government_agency']);
            $table->dropIndex(['office_department_division']);
        });
    }
};
