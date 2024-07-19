<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
            'post_logs',
        ]);

        // hpid
        if (Schema::hasColumn('post_logs', 'hpid')) {
            Schema::table('post_logs', function (Blueprint $table) {
                $table->dropColumn('hpid');
            });
        }
        if (! Schema::hasColumn('post_logs', 'hpid')) {
            Schema::table('post_logs', function (Blueprint $table) {
                $table->string('hpid', 32)->nullable()->after('id');
            });
        }
        DB::table('post_logs')->whereNull('hpid')->select('id')->chunkById(100, function ($postLogs) {
            foreach ($postLogs as $log) {
                DB::table('post_logs')->where('id', $log->id)->update(['hpid' => Str::random(16)]);
            }
        });
        if (Schema::hasColumn('post_logs', 'hpid')) {
            Schema::table('post_logs', function (Blueprint $table) {
                $table->string('hpid', 32)->unique()->change();
            });
        }

        // log
        if (Schema::hasColumn('post_logs', 'parent_post_id')) {
            Schema::table('post_logs', function (Blueprint $table) {
                $table->renameColumn('parent_post_id', 'quoted_post_id');
            });
        }

        if (Schema::hasColumn('post_logs', 'is_plugin_editor')) {
            Schema::table('post_logs', function (Blueprint $table) {
                $table->dropColumn('is_plugin_editor');
                $table->dropColumn('editor_fskey');
                $table->dropColumn('is_comment_disabled');
                $table->dropColumn('is_comment_private');
                $table->dropColumn('map_json');
                $table->dropColumn('read_json');
                $table->dropColumn('user_list_json');
                $table->dropColumn('comment_btn_json');
            });
        }

        if (! Schema::hasColumn('post_logs', 'geotag_id')) {
            Schema::table('post_logs', function (Blueprint $table) {
                $table->unsignedInteger('geotag_id')->nullable()->after('group_id');
                $table->string('lang_tag', 16)->nullable()->index('post_log_lang_tag')->after('content');
                $table->unsignedTinyInteger('is_enabled')->default(1)->after('is_anonymous');
                $table->json('permissions')->nullable()->after('is_anonymous');
                $table->json('more_info')->nullable()->after('is_anonymous');
                $table->json('location_info')->nullable()->after('is_anonymous');
            });
        }
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
};
