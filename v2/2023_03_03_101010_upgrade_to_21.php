<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Helpers\CacheHelper;
use App\Models\Config;
use App\Models\Language;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;

class UpgradeTo21 extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to 21 (fresns v2.7.2)
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["2.7.2 => {$currentVersion}"]);

        if (version_compare('2.7.2', $currentVersion) == -1) {
            return;
        }

        // lang pack add key
        $languagePack = Config::where('item_key', 'language_pack')->first();
        if ($languagePack) {
            $packData = $languagePack->item_value;

            $addPackKeys = [
                [
                    'name' => 'renewal',
                    'canDelete' => false,
                ],
                [
                    'name' => 'privateContentHide',
                    'canDelete' => false,
                ],
                [
                    'name' => 'privateContentShowOld',
                    'canDelete' => false,
                ],
            ];

            $newData = array_merge($packData, $addPackKeys);

            $languagePack->item_value = $newData;
            $languagePack->save();
        }

        // lang pack add content
        $langPackContents = Language::where('table_name', 'configs')->where('table_column', 'item_value')->where('table_key', 'language_pack_contents')->get();
        foreach ($langPackContents as $packContent) {
            $content = (object) json_decode($packContent->lang_content, true);

            $langAddContent = match ($packContent->lang_tag) {
                'en' => [
                    'renewal' => 'Renewal',
                    'privateContentHide' => 'Membership has expired, all site content is no longer visible.',
                    'privateContentShowOld' => 'Membership has expired, you can only browse content from before the expiration date and no longer have access to new content.',
                ],
                'zh-Hans' => [
                    'renewal' => '续期',
                    'privateContentHide' => '会员已到期，站点内容全部不可见',
                    'privateContentShowOld' => '会员已到期，仅能浏览到期前内容，不再显示新内容',
                ],
                'zh-Hant' => [
                    'renewal' => '續期',
                    'privateContentHide' => '會員已到期，站點內容全部不可見',
                    'privateContentShowOld' => '會員已到期，僅能瀏覽到期前內容，不再顯示新內容',
                ],
                default => null,
            };

            if (empty($langAddContent)) {
                continue;
            }

            $langNewContent = (object) array_merge((array) $content, (array) $langAddContent);

            $packContent->lang_content = json_encode($langNewContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
            $packContent->save();
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
