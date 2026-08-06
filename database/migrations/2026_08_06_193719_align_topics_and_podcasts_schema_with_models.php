<?php

use App\Enums\TopicStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The topics/podcasts create-table migrations were edited after they had
     * already run on this database, so the live schema drifted from what the
     * models/services/form-requests now expect. This brings the existing
     * tables in line without touching environments where the original
     * migrations already produced the correct columns.
     */
    public function up(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            if (!Schema::hasColumn('topics', 'status')) {
                $table->enum('status', array_column(TopicStatus::cases(), 'value'))
                    ->default(TopicStatus::PENDING->value)
                    ->after('name_ar');
            }
            if (!Schema::hasColumn('topics', 'created_by')) {
                $table->foreignId('created_by')->constrained('users')->after('status');
            }
        });

        if (Schema::hasColumn('podcasts', 'level_id')) {
            Schema::table('podcasts', function (Blueprint $table) {
                $table->dropForeign(['level_id']);
                $table->dropColumn('level_id');
            });
        }

        Schema::table('podcasts', function (Blueprint $table) {
            if (!Schema::hasColumn('podcasts', 'name_en')) {
                $table->string('name_en')->after('topic_id');
            }
            if (!Schema::hasColumn('podcasts', 'name_ar')) {
                $table->string('name_ar')->after('name_en');
            }
            if (!Schema::hasColumn('podcasts', 'created_by')) {
                $table->foreignId('created_by')->constrained('users')->after('point_required');
            }
        });
    }

    public function down(): void
    {
        Schema::table('podcasts', function (Blueprint $table) {
            if (Schema::hasColumn('podcasts', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('podcasts', 'name_ar')) {
                $table->dropColumn('name_ar');
            }
            if (Schema::hasColumn('podcasts', 'name_en')) {
                $table->dropColumn('name_en');
            }
        });

        Schema::table('topics', function (Blueprint $table) {
            if (Schema::hasColumn('topics', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('topics', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
