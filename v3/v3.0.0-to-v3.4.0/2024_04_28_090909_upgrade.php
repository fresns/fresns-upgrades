<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\CodeMessage;
use App\Models\Config;
use App\Models\LanguagePack;
use App\Models\SessionLog;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v3.1.0
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 3.1.0"]);

        SessionLog::where('type', 32)->update(['type' => 33]);

        SessionLog::where('type', 31)->update(['type' => 32]);

        SessionLog::where('type', 30)->update(['type' => 31]);

        SessionLog::where('type', 29)->update(['type' => 30]);

        SessionLog::where('type', 28)->update(['type' => 29]);

        SessionLog::where('type', 27)->update(['type' => 28]);

        SessionLog::where('type', 26)->update(['type' => 27]);

        SessionLog::where('type', 25)->update(['type' => 26]);

        SessionLog::where('type', 24)->update(['type' => 25]);

        SessionLog::where('type', 23)->update(['type' => 24]);

        SessionLog::where('type', 22)->update(['type' => 23]);

        SessionLog::where('type', 21)->update(['type' => 22]);

        SessionLog::where('type', 20)->update(['type' => 21]);

        SessionLog::where('type', 19)->update(['type' => 20]);

        SessionLog::where('type', 18)->update(['type' => 19]);

        SessionLog::where('type', 17)->update(['type' => 18]);

        SessionLog::where('type', 16)->update(['type' => 17]);

        SessionLog::where('type', 15)->update(['type' => 16]);

        SessionLog::where('type', 14)->update(['type' => 15]);

        SessionLog::where('type', 13)->update(['type' => 14]);

        SessionLog::where('type', 12)->update(['type' => 13]);

        SessionLog::where('type', 11)->update(['type' => 12]);

        Config::updateOrCreate([
            'item_key' => 'account_age_verification',
        ], [
            'item_value' => 'true',
            'item_type' => 'boolean',
            'is_multilingual' => 0,
            'is_custom' => 0,
            'is_api' => 0,
        ]);

        Config::updateOrCreate([
            'item_key' => 'account_age_min_required',
        ], [
            'item_value' => '13',
            'item_type' => 'number',
            'is_multilingual' => 0,
            'is_custom' => 0,
            'is_api' => 0,
        ]);

        CodeMessage::updateOrCreate([
            'app_fskey' => 'Fresns',
            'code' => 34114,
        ], [
            'messages' => [
                'en' => 'We are sorry, but according to our Terms of Use, you are not of legal age to use our services. This is to ensure that we comply with applicable laws and to protect the safety of all our users.',
                'zh-Hans' => '很抱歉，根据我们的使用条款，您的年龄无法使用我们的服务。这是为了确保我们遵守相关法律和保护所有用户的安全。',
                'zh-Hant' => '很抱歉，根據我們的使用條款，您的年齡無法使用我們的服務。 這是為了確保我們遵守相關法律和保護所有使用者的安全。',
            ],
        ]);

        LanguagePack::updateOrCreate([
            'lang_key' => 'appearance',
        ], [
            'lang_values' => [
                'en' => 'Appearance',
                'zh-Hans' => '外观',
                'zh-Hant' => '外貌',
            ],
            'is_custom' => 0,
        ]);

        LanguagePack::updateOrCreate([
            'lang_key' => 'light',
        ], [
            'lang_values' => [
                'en' => 'Light',
                'zh-Hans' => '浅色',
                'zh-Hant' => '淺色',
            ],
            'is_custom' => 0,
        ]);

        LanguagePack::updateOrCreate([
            'lang_key' => 'dark',
        ], [
            'lang_values' => [
                'en' => 'Dark',
                'zh-Hans' => '深色',
                'zh-Hant' => '深色',
            ],
            'is_custom' => 0,
        ]);
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
};
