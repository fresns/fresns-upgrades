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
            'comment_logs',
        ]);

        // hcid
        if (Schema::hasColumn('comment_logs', 'hcid')) {
            Schema::table('comment_logs', function (Blueprint $table) {
                $table->dropColumn('hcid');
            });
        }
        if (! Schema::hasColumn('comment_logs', 'hcid')) {
            Schema::table('comment_logs', function (Blueprint $table) {
                $table->string('hcid', 32)->nullable()->after('id');
            });
        }
        DB::table('comment_logs')->whereNull('hcid')->select('id')->chunkById(100, function ($commentLogs) {
            foreach ($commentLogs as $log) {
                DB::table('comment_logs')->where('id', $log->id)->update(['hcid' => Str::random(16)]);
            }
        });
        if (Schema::hasColumn('comment_logs', 'hcid')) {
            Schema::table('comment_logs', function (Blueprint $table) {
                $table->string('hcid', 32)->unique()->change();
            });
        }

        // log
        if (Schema::hasColumn('comment_logs', 'is_plugin_editor')) {
            Schema::table('comment_logs', function (Blueprint $table) {
                $table->dropColumn('is_plugin_editor');
                $table->dropColumn('editor_fskey');
                $table->dropColumn('map_json');
            });
        }

        if (! Schema::hasColumn('comment_logs', 'geotag_id')) {
            Schema::table('comment_logs', function (Blueprint $table) {
                $table->unsignedInteger('geotag_id')->nullable()->after('parent_comment_id');
                $table->string('lang_tag', 16)->nullable()->index('comment_log_lang_tag')->after('content');
                $table->unsignedTinyInteger('is_enabled')->default(1)->after('is_anonymous');
                $table->json('permissions')->nullable()->after('is_anonymous');
                $table->json('more_info')->nullable()->after('is_anonymous');
                $table->json('location_info')->nullable()->after('is_anonymous');
                $table->unsignedTinyInteger('is_private')->default(0)->after('is_anonymous');
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
