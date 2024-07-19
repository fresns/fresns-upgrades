<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\LanguagePack;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v3.1.2
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 3.1.2"]);

        LanguagePack::updateOrCreate([
            'lang_key' => 'she',
        ], [
            'lang_values' => [
                'en' => 'She',
                'zh-Hans' => '她',
                'zh-Hant' => '她',
            ],
            'is_custom' => 0,
        ]);

        LanguagePack::updateOrCreate([
            'lang_key' => 'he',
        ], [
            'lang_values' => [
                'en' => 'He',
                'zh-Hans' => '他',
                'zh-Hant' => '他',
            ],
            'is_custom' => 0,
        ]);

        LanguagePack::updateOrCreate([
            'lang_key' => 'they',
        ], [
            'lang_values' => [
                'en' => 'They',
                'zh-Hans' => 'TA',
                'zh-Hant' => 'TA',
            ],
            'is_custom' => 0,
        ]);

        LanguagePack::updateOrCreate([
            'lang_key' => 'accountCenterTip',
        ], [
            'lang_values' => [
                'en' => 'Please go to the Account Center for this feature',
                'zh-Hans' => '该功能请前往账户中心操作',
                'zh-Hant' => '此功能請前往帳戶中心操作',
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
