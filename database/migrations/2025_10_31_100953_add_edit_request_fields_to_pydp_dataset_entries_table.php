<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pydp_dataset_entries', function (Blueprint $table) {
            // Add columns if they don't exist
            if (!Schema::hasColumn('pydp_dataset_entries', 'submission_notes')) {
                $table->text('submission_notes')->nullable()->after('remarks');
            }
            
            if (!Schema::hasColumn('pydp_dataset_entries', 'edit_requested')) {
                $table->boolean('edit_requested')->default(false)->after('submission_notes');
            }
            
            if (!Schema::hasColumn('pydp_dataset_entries', 'edit_request_reason')) {
                $table->text('edit_request_reason')->nullable()->after('edit_requested');
            }
            
            if (!Schema::hasColumn('pydp_dataset_entries', 'edit_requested_at')) {
                $table->timestamp('edit_requested_at')->nullable()->after('edit_request_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pydp_dataset_entries', function (Blueprint $table) {
            if (Schema::hasColumn('pydp_dataset_entries', 'submission_notes')) {
                $table->dropColumn('submission_notes');
            }
            if (Schema::hasColumn('pydp_dataset_entries', 'edit_requested')) {
                $table->dropColumn('edit_requested');
            }
            if (Schema::hasColumn('pydp_dataset_entries', 'edit_request_reason')) {
                $table->dropColumn('edit_request_reason');
            }
            if (Schema::hasColumn('pydp_dataset_entries', 'edit_requested_at')) {
                $table->dropColumn('edit_requested_at');
            }
        });
    }
};