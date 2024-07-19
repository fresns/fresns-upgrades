<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Helpers\CacheHelper;
use App\Models\CodeMessage;
use App\Models\Config;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;

class UpgradeTo25 extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v2.10.0
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["2.10.0 => {$currentVersion}"]);

        if (version_compare('2.10.0', $currentVersion) == -1) {
            return;
        }

        $sitePrivateWhitelistRoles = Config::where('item_key', 'site_private_whitelist_roles')->first();
        if (! $sitePrivateWhitelistRoles) {
            $newConfig = new Config;
            $newConfig->item_key = 'site_private_whitelist_roles';
            $newConfig->item_value = '[]';
            $newConfig->item_type = 'array';
            $newConfig->item_tag = 'general';
            $newConfig->is_multilingual = 0;
            $newConfig->is_custom = 0;
            $newConfig->is_api = 1;
            $newConfig->save();
        }

        // code messages
        $code34404Messages = CodeMessage::where('plugin_unikey', 'Fresns')->where('code', 34404)->where('lang_tag', 'en')->first();
        if (empty($code34404Messages)) {
            CodeMessage::updateOrCreate([
                'plugin_unikey' => 'Fresns',
                'code' => '34404',
                'lang_tag' => 'en',
            ],
            [
                'message' => 'Connect token is disabled',
            ]);
            CodeMessage::updateOrCreate([
                'plugin_unikey' => 'Fresns',
                'code' => '34404',
                'lang_tag' => 'zh-Hans',
            ],
            [
                'message' => '互联凭证已禁用',
            ]);
            CodeMessage::updateOrCreate([
                'plugin_unikey' => 'Fresns',
                'code' => '34404',
                'lang_tag' => 'zh-Hant',
            ],
            [
                'message' => '互聯憑證已禁用',
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
