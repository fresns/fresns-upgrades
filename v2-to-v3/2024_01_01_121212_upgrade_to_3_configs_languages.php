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
     * Upgrade to v3.0.0
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', [
            "{$currentVersion} >> 3.0.0",
            'configs languages',
        ]);

        $configKeys = [
            'bulletin_name',
            'bulletin_contents',
            'site_name',
            'site_desc',
            'site_intro',
            'user_name',
            'user_uid_name',
            'user_username_name',
            'user_nickname_name',
            'user_role_name',
            'user_bio_name',
            'extcredits1_name',
            'extcredits1_unit',
            'extcredits2_name',
            'extcredits2_unit',
            'extcredits3_name',
            'extcredits3_unit',
            'extcredits4_name',
            'extcredits4_unit',
            'extcredits5_name',
            'extcredits5_unit',
            'wallet_currency_name',
            'wallet_currency_unit',
            'group_name',
            'hashtag_name',
            'group_name',
            'hashtag_name',
            'post_name',
            'comment_name',
            'publish_post_name',
            'publish_comment_name',
        ];
        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configs as $config) {
            $itemKey = $config->item_key;

            $languages = Language::where('table_name', 'configs')->where('table_key', $itemKey)->get();

            $langValue = null;
            foreach ($languages as $lang) {
                $langValue[$lang->lang_tag] = $lang->lang_content;
            }

            $itemValue = $langValue ? json_encode($langValue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK) : '{"en":"Untitled","zh-Hans":"未命名","zh-Hant":"未命名"}';

            $config->update([
                'item_value' => $itemValue,
                'item_type' => 'object',
            ]);
        }

        // verifycode templates
        $verifycodeKeys = [
            'verifycode_template1',
            'verifycode_template2',
            'verifycode_template3',
            'verifycode_template4',
            'verifycode_template5',
            'verifycode_template6',
            'verifycode_template7',
            'verifycode_template8',
        ];
        $verifyCodeConfigs = Config::whereIn('item_key', $verifycodeKeys)->get();

        foreach ($verifyCodeConfigs as $verifyCodeConfig) {
            $itemValue = $verifyCodeConfig->item_value;

            $emailStatus = false;
            $emailTemplates = [];
            $smsStatus = false;
            $smsTemplates = [];
            foreach ($itemValue as $template) {
                $templatesStatus = $template['isEnabled'];
                $templatesArr = $template['template'] ?? $template['templates'] ?? [];

                if ($template['type'] == 'email') {
                    $emailStatus = $templatesStatus;

                    foreach ($templatesArr as $tmp) {
                        $langTag = $tmp['langTag'];
                        $title = $tmp['title'];
                        $content = $tmp['content'];

                        $emailTemplates[$langTag] = [
                            'title' => $title,
                            'content' => $content,
                        ];
                    };
                }
                if ($template['type'] == 'sms') {
                    $smsStatus = $templatesStatus;

                    foreach ($templatesArr as $tmp) {
                        $langTag = $tmp['langTag'];
                        $signName = $tmp['signName'];
                        $templateCode = $tmp['templateCode'];
                        $codeParam = $tmp['codeParam'];

                        $smsTemplates[$langTag] = [
                            'signName' => $signName,
                            'templateCode' => $templateCode,
                            'codeParam' => $codeParam,
                        ];
                    };
                }
            }

            $newValue = [
                'email' => [
                    'status' => $emailStatus,
                    'templates' => $emailTemplates,
                ],
                'sms' => [
                    'status' => $smsStatus,
                    'templates' => $smsTemplates,
                ],
            ];

            $valueString = json_encode($newValue);

            $verifyCodeConfig->update([
                'item_value' => $valueString,
                'item_type' => 'object',
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
