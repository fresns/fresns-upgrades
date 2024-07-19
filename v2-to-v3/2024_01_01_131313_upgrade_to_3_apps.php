<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\App;
use App\Models\AppUsage;
use App\Models\Config;
use App\Models\FileUsage;
use App\Models\Language;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            'apps',
        ]);

        if (Schema::hasColumn('apps', 'is_standalone')) {
            $apps = App::get();

            foreach ($apps as $app) {
                $app->update([
                    'type' => $app->is_standalone ? App::TYPE_APP_DOWNLOAD : App::TYPE_PLUGIN,
                ]);
            }
        }

        if (Schema::hasColumn('apps', 'scene')) {
            Schema::table('apps', function (Blueprint $table) {
                $table->renameColumn('scene', 'panel_usages');
            });
        }

        if (Schema::hasColumn('apps', 'plugin_host')) {
            Schema::table('apps', function (Blueprint $table) {
                $table->renameColumn('plugin_host', 'app_host');
            });
        }

        if (Schema::hasColumn('app_usages', 'data_sources')) {
            Schema::table('app_usages', function (Blueprint $table) {
                $table->dropColumn('data_sources');
            });
        }

        if (Schema::hasColumn('app_usages', 'rating')) {
            Schema::table('app_usages', function (Blueprint $table) {
                $table->renameColumn('rating', 'sort_order');
            });
        }

        if (! Schema::hasColumn('app_usages', 'name')) {
            Schema::table('app_usages', function (Blueprint $table) {
                $table->json('name')->nullable()->after('usage_type');
            });
        }


        $appUsages = AppUsage::all();
        foreach ($appUsages as $appUsage) {
            $languageItems = Language::where('table_name', 'plugin_usages')->where('table_id', $appUsage->id)->get();

            $langItemValue = null;
            foreach ($languageItems as $langItem) {
                $langItemValue[$langItem->lang_tag] = $langItem->lang_content;
            }

            $newValue = $langItemValue ?? [
                'en' => 'Untitled',
                'zh-Hans' => '未命名',
                'zh-Hant' => '未命名,'
            ];

            $appUsage->update([
                'name' => $newValue,
            ]);
        }

        $fileUsages = FileUsage::where('table_name', 'plugin_usages')->get();
        foreach ($fileUsages as $fileUsage) {
            $fileUsage->update([
                'table_name' => 'app_usages',
            ]);
        }

        $crontabItems = Config::where('item_key', 'crontab_items')->first();
        $crontabArr = $crontabItems?->item_value;
        if ($crontabArr) {
            $crontabAsString = json_encode($crontabArr);
            $crontabItemsReplaced = Str::replace('checkPluginsVersions', 'checkAppsVersions', $crontabAsString);
            $crontabItems?->update([
                'item_value' => $crontabItemsReplaced,
            ]);
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
