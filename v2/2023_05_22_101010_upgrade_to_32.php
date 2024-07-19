<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Helpers\CacheHelper;
use App\Models\Config;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UpgradeTo32 extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v2.13.0
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 2.12.1"]);

        if (! Schema::hasColumn('user_stats', 'view_me_count')) {
            Schema::table('user_stats', function (Blueprint $table) {
                $table->unsignedInteger('view_me_count')->default(0)->after('block_comment_count');
            });
        }

        if (! Schema::hasColumn('groups', 'view_count')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->unsignedInteger('view_count')->default(0)->after('permissions');
            });
        }

        if (! Schema::hasColumn('hashtags', 'view_count')) {
            Schema::table('hashtags', function (Blueprint $table) {
                $table->unsignedInteger('view_count')->default(0)->after('cover_file_url');
            });
        }

        if (! Schema::hasColumn('posts', 'view_count')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->unsignedInteger('view_count')->default(0)->after('digest_state');
            });
        }

        if (! Schema::hasColumn('comments', 'view_count')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->unsignedInteger('view_count')->default(0)->after('digest_state');
            });
        }

        // subject
        $subscribeItems = Config::where('item_key', 'subscribe_items')->first();
        $subArr = $subscribeItems?->item_value;
        if ($subArr) {
            $crontabAsString = json_encode($subArr);
            $subscribeItemsReplaced = Str::replace('subTableName', 'subject', $crontabAsString);
            $subscribeItems?->update([
                'item_value' => $subscribeItemsReplaced,
            ]);
        }

        CacheHelper::clearAllCache();
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
}
