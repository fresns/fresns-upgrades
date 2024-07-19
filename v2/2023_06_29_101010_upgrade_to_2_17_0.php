<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Utilities\AppUtility;
use App\Utilities\ConfigUtility;
use Illuminate\Database\Migrations\Migration;

class UpgradeTo2170 extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v2.17.0
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 2.17.0"]);

        $newConfigItems = [
            [
                'item_key' => 'user_follower_name',
                'item_value' => 'Followers',
                'item_type' => 'string',
                'item_tag' => 'renames',
                'is_multilingual' => 1,
                'is_custom' => 0,
                'is_api' => 1,
                'language_values' => [
                    'en' => 'Followers',
                    'zh-Hans' => '关注者',
                    'zh-Hant' => '跟隨者',
                ],
            ],
            [
                'item_key' => 'group_follower_name',
                'item_value' => 'Members',
                'item_type' => 'string',
                'item_tag' => 'renames',
                'is_multilingual' => 1,
                'is_custom' => 0,
                'is_api' => 1,
                'language_values' => [
                    'en' => 'Members',
                    'zh-Hans' => '成员',
                    'zh-Hant' => '成員',
                ],
            ],
            [
                'item_key' => 'hashtag_follower_name',
                'item_value' => 'Subscribers',
                'item_type' => 'string',
                'item_tag' => 'renames',
                'is_multilingual' => 1,
                'is_custom' => 0,
                'is_api' => 1,
                'language_values' => [
                    'en' => 'Subscribers',
                    'zh-Hans' => '订阅者',
                    'zh-Hant' => '訂閱者',
                ],
            ],
            [
                'item_key' => 'post_follower_name',
                'item_value' => 'Collectors',
                'item_type' => 'string',
                'item_tag' => 'renames',
                'is_multilingual' => 1,
                'is_custom' => 0,
                'is_api' => 1,
                'language_values' => [
                    'en' => 'Collectors',
                    'zh-Hans' => '收藏者',
                    'zh-Hant' => '收藏者',
                ],
            ],
            [
                'item_key' => 'comment_follower_name',
                'item_value' => 'Collectors',
                'item_type' => 'string',
                'item_tag' => 'renames',
                'is_multilingual' => 1,
                'is_custom' => 0,
                'is_api' => 1,
                'language_values' => [
                    'en' => 'Collectors',
                    'zh-Hans' => '收藏者',
                    'zh-Hant' => '收藏者',
                ],
            ],
        ];

        ConfigUtility::addFresnsConfigItems($newConfigItems);
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
}
