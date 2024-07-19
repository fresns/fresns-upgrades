<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Helpers\CacheHelper;
use App\Models\Config;
use App\Utilities\AppUtility;
use App\Utilities\ConfigUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v2.19.1
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 2.19.1"]);

        $crontabItems = Config::where('item_key', 'crontab_items')->first();
        $crontabArr = $crontabItems?->item_value;
        if ($crontabArr) {
            $crontabAsString = json_encode($crontabArr);
            $crontabItemsReplaced = Str::replace('checkExtensionsVersion', 'checkPluginsVersions', $crontabAsString);
            $crontabItems?->update([
                'item_value' => $crontabItemsReplaced,
            ]);
        }

        ConfigUtility::addFresnsConfigItems([
            [
                'item_key' => 'site_intro',
                'item_value' => 'Site Introduction',
                'item_type' => 'string',
                'item_tag' => 'general',
                'is_multilingual' => 1,
                'is_custom' => 0,
                'is_api' => 1,
                'language_values' => [
                    'en' => '# About Us

To be edited',
                    'zh-Hans' => '# 关于我们

待编辑',
                    'zh-Hant' => '# 關於我們

待編輯',
                ],
            ],
        ]);

        CacheHelper::clearAllCache();
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
};
