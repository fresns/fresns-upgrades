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
            'posts',
        ]);

        if (Schema::hasColumn('posts', 'writing_direction')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('writing_direction');
            });
        }

        if (Schema::hasColumn('posts', 'map_longitude')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('map_longitude');
                $table->dropColumn('map_latitude');
            });
        }

        if (Schema::hasColumn('posts', 'parent_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->renameColumn('parent_id', 'quoted_post_id');
                $table->renameColumn('post_count', 'quote_count');
            });
        }

        if (! Schema::hasColumn('posts', 'geotag_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->unsignedInteger('geotag_id')->default(0)->index('post_geotag_id')->after('group_id');
            });
        }

        if (Schema::hasColumn('posts', 'latest_edit_at')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->renameColumn('latest_edit_at', 'last_edit_at');
                $table->renameColumn('latest_comment_at', 'last_comment_at');
            });
        }

        if (! Schema::hasColumn('posts', 'edit_count')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->unsignedInteger('edit_count')->default(0)->after('quote_count');
            });
        }

        if (! Schema::hasColumn('posts', 'permissions')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->json('permissions')->nullable()->after('comment_block_count');
            });
        }

        if (! Schema::hasColumn('posts', 'more_info')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->json('more_info')->nullable()->after('comment_block_count');
            });
        }

        if (Schema::hasColumn('post_users', 'more_json')) {
            Schema::table('post_users', function (Blueprint $table) {
                $table->renameColumn('more_json', 'more_info');
            });
        }

        if (Schema::hasColumn('post_auths', 'object_id')) {
            Schema::table('post_auths', function (Blueprint $table) {
                $table->renameColumn('type', 'auth_type');
                $table->renameColumn('object_id', 'auth_id');
            });
        }

        Schema::dropIfExists('post_appends');
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
};
