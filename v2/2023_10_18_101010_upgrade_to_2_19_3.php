<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\Config;
use App\Models\Language;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v2.19.3
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 2.19.3"]);

        $china_psb_filing = Config::where('item_key', 'china_psb_filing')->first();
        if ($china_psb_filing) {
            $china_psb_filing->update([
                'item_key' => 'china_mps_filing',
            ]);
        }

        // lang pack add key
        $languagePack = Config::where('item_key', 'language_pack')->first();
        if ($languagePack) {
            $packData = $languagePack->item_value;

            $addPackKeys = [
                [
                    'name' => 'reviewApp',
                    'canDelete' => false,
                ],
            ];

            $newData = array_merge($packData, $addPackKeys);

            $languagePack->item_value = $newData;
            $languagePack->save();
        }

        $langPackContents = Language::where('table_name', 'configs')->where('table_column', 'item_value')->where('table_key', 'language_pack_contents')->get();
        foreach ($langPackContents as $packContent) {
            $content = $packContent->lang_content;
            if (empty($content)) {
                continue;
            }

            $langAddContent = match ($packContent->lang_tag) {
                'en' => [
                    'reviewApp' => 'Review App',
                ],
                'zh-Hans' => [
                    'reviewApp' => '评价 App',
                ],
                'zh-Hant' => [
                    'reviewApp' => '評價 App',
                ],
                default => null,
            };

            if (empty($langAddContent)) {
                continue;
            }

            // merge by key de-duplication
            $newContent = (object) json_decode($content, true);

            $langNewContent = clone $newContent;

            foreach ($langAddContent as $key => $value) {
                if (! property_exists($newContent, $key)) {
                    $langNewContent->$key = $value;
                }
            }

            $packContent->lang_content = json_encode($langNewContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
            $packContent->save();
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
