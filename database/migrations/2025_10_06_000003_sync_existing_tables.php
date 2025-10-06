<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SyncExistingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // news: ensure columns exist and constraints
        if (Schema::hasTable('news')) {
            Schema::table('news', function (Blueprint $table) {
                if (!Schema::hasColumn('news', 'image_public_id')) {
                    $table->string('image_public_id')->nullable()->after('imageUrl');
                }
                if (!Schema::hasColumn('news', 'slug')) {
                    $table->string('slug')->unique()->after('title');
                } else {
                    // try to add unique index if not exists by checking information_schema
                    $exists = DB::selectOne("SELECT COUNT(1) AS cnt FROM information_schema.STATISTICS WHERE table_schema = DATABASE() AND table_name = 'news' AND (INDEX_NAME = 'news_slug_unique' OR (COLUMN_NAME='slug' AND NON_UNIQUE=0))");
                    if (empty($exists) || $exists->cnt == 0) {
                        $table->unique('slug');
                    }
                }
            });
        }

        // schedules: ensure unique constraint exists
        if (Schema::hasTable('schedules')) {
            Schema::table('schedules', function (Blueprint $table) {
                // Add composite unique only if not exists
                $exists = DB::selectOne("SELECT COUNT(1) AS cnt FROM information_schema.TABLE_CONSTRAINTS tc JOIN information_schema.KEY_COLUMN_USAGE kcu USING (CONSTRAINT_NAME, TABLE_NAME, TABLE_SCHEMA) WHERE tc.TABLE_SCHEMA = DATABASE() AND tc.TABLE_NAME='schedules' AND tc.CONSTRAINT_TYPE='UNIQUE' AND kcu.COLUMN_NAME IN ('club_id','day_of_week')");
                if (empty($exists) || $exists->cnt == 0) {
                    try {
                        $table->unique(['club_id', 'day_of_week'], 'schedules_club_day_unique');
                    } catch (\Exception $e) {
                        // ignore
                    }
                }
            });
        }

        // students: do not change column nullability here because changing columns
        // requires doctrine/dbal. If you want to enforce not-null, either install
        // doctrine/dbal or create a manual migration that copies data to a new
        // column and renames it. For now we skip altering students.name.
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasTable('news')) {
            Schema::table('news', function (Blueprint $table) {
                // don't drop columns in down to avoid data loss
            });
        }
    }
}
