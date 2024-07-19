<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Helpers\CacheHelper;
use App\Models\CodeMessage;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpgradeTo24 extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v2.9.0
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["2.9.0 => {$currentVersion}"]);

        if (version_compare('2.9.0', $currentVersion) == -1) {
            return;
        }

        try {
            Schema::table('archive_usages', function (Blueprint $table) {
                $table->index('archive_id', 'usage_archive_id');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('domain_link_usages', function (Blueprint $table) {
                $table->index('link_id', 'usage_link_id');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('extend_usages', function (Blueprint $table) {
                $table->index('extend_id', 'usage_extend_id');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('file_usages', function (Blueprint $table) {
                $table->index('file_id', 'usage_file_id');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('hashtag_usages', function (Blueprint $table) {
                $table->index('hashtag_id', 'usage_hashtag_id');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('operation_usages', function (Blueprint $table) {
                $table->index('operation_id', 'usage_operation_id');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('plugin_usages', function (Blueprint $table) {
                $table->index('usage_type', 'plugin_usage_type');
                $table->index('group_id', 'plugin_usage_group_id');
            });
        } catch (\Exception $e) {}

        // code messages
        $code36120Messages = CodeMessage::where('plugin_unikey', 'Fresns')->where('code', 36120)->where('lang_tag', 'en')->first();
        if (empty($code36120Messages)) {
            CodeMessage::updateOrCreate([
                'plugin_unikey' => 'Fresns',
                'code' => '36120',
                'lang_tag' => 'en',
            ],
            [
                'message' => 'The daily publish limit has been reached, please publish again tomorrow',
            ]);
            CodeMessage::updateOrCreate([
                'plugin_unikey' => 'Fresns',
                'code' => '36120',
                'lang_tag' => 'zh-Hans',
            ],
            [
                'message' => '发表已达每日上限，请明天再发表',
            ]);
            CodeMessage::updateOrCreate([
                'plugin_unikey' => 'Fresns',
                'code' => '36120',
                'lang_tag' => 'zh-Hant',
            ],
            [
                'message' => '發表已達每日上限，請明天再發表',
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
