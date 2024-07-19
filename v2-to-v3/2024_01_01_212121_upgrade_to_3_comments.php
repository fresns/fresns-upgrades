<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v3.0.0
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', [
            "{$currentVersion} >> 3.0.0",
            'comments',
        ]);

        if (Schema::hasColumn('comments', 'writing_direction')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropColumn('writing_direction');
            });
        }

        if (Schema::hasColumn('comments', 'map_longitude')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropColumn('map_longitude');
                $table->dropColumn('map_latitude');
            });
        }

        if (! Schema::hasColumn('comments', 'geotag_id')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->unsignedTinyInteger('privacy_state')->default(1)->index('comment_privacy_state')->after('is_anonymous');
            });
        }

        if (! Schema::hasColumn('comments', 'geotag_id')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->unsignedInteger('geotag_id')->default(0)->index('comment_geotag_id')->after('user_id');
            });
        }

        if (Schema::hasColumn('comments', 'latest_edit_at')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->renameColumn('latest_edit_at', 'last_edit_at');
                $table->renameColumn('latest_comment_at', 'last_comment_at');
            });
        }

        if (! Schema::hasColumn('comments', 'edit_count')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->unsignedInteger('edit_count')->default(0)->after('comment_block_count');
            });
        }

        if (! Schema::hasColumn('comments', 'permissions')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->json('permissions')->nullable()->after('comment_block_count');
            });
        }

        if (! Schema::hasColumn('comments', 'more_info')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->json('more_info')->nullable()->after('comment_block_count');
            });
        }

        Schema::dropIfExists('comment_appends');
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
};
