<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pydp_dataset_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('pydp_dataset_entries', 'submission_status')) {
                $table->enum('submission_status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft')->after('remarks');
            }
            if (!Schema::hasColumn('pydp_dataset_entries', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('submission_status');
            }
            if (!Schema::hasColumn('pydp_dataset_entries', 'submitted_by')) {
                $table->string('submitted_by')->nullable()->after('submitted_at');
            }
            if (!Schema::hasColumn('pydp_dataset_entries', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('submitted_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pydp_dataset_entries', function (Blueprint $table) {
            $columns = ['submission_status', 'submitted_at', 'submitted_by', 'admin_notes'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('pydp_dataset_entries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};