<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\Config;
use App\Models\Language;
use App\Utilities\AppUtility;
use App\Utilities\ConfigUtility;
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
            'interaction configs',
        ]);

        $configs = Config::whereIn('item_key', [
            'like_user_setting',
            'dislike_user_setting',
            'follow_user_setting',
            'block_user_setting',

            'like_group_setting',
            'dislike_group_setting',
            'follow_group_setting',
            'block_group_setting',

            'like_hashtag_setting',
            'dislike_hashtag_setting',
            'follow_hashtag_setting',
            'block_hashtag_setting',

            'like_post_setting',
            'dislike_post_setting',
            'follow_post_setting',
            'block_post_setting',

            'like_comment_setting',
            'dislike_comment_setting',
            'follow_comment_setting',
            'block_comment_setting',

            'group_likers',
            'hashtag_likers',
            'post_likers',
            'comment_likers',
            'group_liker_count',
            'hashtag_liker_count',
            'post_liker_count',
            'comment_liker_count',

            'group_dislikers',
            'hashtag_dislikers',
            'post_dislikers',
            'comment_dislikers',
            'group_disliker_count',
            'hashtag_disliker_count',
            'post_disliker_count',
            'comment_disliker_count',

            'group_followers',
            'hashtag_followers',
            'post_followers',
            'comment_followers',
            'group_follower_count',
            'hashtag_follower_count',
            'post_follower_count',
            'comment_follower_count',

            'group_blockers',
            'hashtag_blockers',
            'post_blockers',
            'comment_blockers',
            'group_blocker_count',
            'hashtag_blocker_count',
            'post_blocker_count',
            'comment_blocker_count',
        ])->get();

        foreach ($configs as $config) {
            $itemKey = $config->item_key;

            $newItemKey = match ($itemKey) {
                'like_user_setting' => 'user_like_enabled',
                'dislike_user_setting' => 'user_dislike_enabled',
                'follow_user_setting' => 'user_follow_enabled',
                'block_user_setting' => 'user_block_enabled',

                'like_group_setting' => 'group_like_enabled',
                'dislike_group_setting' => 'group_dislike_enabled',
                'follow_group_setting' => 'group_follow_enabled',
                'block_group_setting' => 'group_block_enabled',

                'like_hashtag_setting' => 'hashtag_like_enabled',
                'dislike_hashtag_setting' => 'hashtag_dislike_enabled',
                'follow_hashtag_setting' => 'hashtag_follow_enabled',
                'block_hashtag_setting' => 'hashtag_block_enabled',

                'like_post_setting' => 'post_like_enabled',
                'dislike_post_setting' => 'post_dislike_enabled',
                'follow_post_setting' => 'post_follow_enabled',
                'block_post_setting' => 'post_block_enabled',

                'like_comment_setting' => 'comment_like_enabled',
                'dislike_comment_setting' => 'comment_dislike_enabled',
                'follow_comment_setting' => 'comment_follow_enabled',
                'block_comment_setting' => 'comment_block_enabled',

                'group_likers' => 'group_like_public_record',
                'hashtag_likers' => 'hashtag_like_public_record',
                'post_likers' => 'post_like_public_record',
                'comment_likers' => 'comment_like_public_record',
                'group_liker_count' => 'group_like_public_count',
                'hashtag_liker_count' => 'hashtag_like_public_count',
                'post_liker_count' => 'post_like_public_count',
                'comment_liker_count' => 'comment_like_public_count',

                'group_dislikers' => 'group_dislike_public_record',
                'hashtag_dislikers' => 'hashtag_dislike_public_record',
                'post_dislikers' => 'post_dislike_public_record',
                'comment_dislikers' => 'comment_dislike_public_record',
                'group_disliker_count' => 'group_dislike_public_count',
                'hashtag_disliker_count' => 'hashtag_dislike_public_count',
                'post_disliker_count' => 'post_dislike_public_count',
                'comment_disliker_count' => 'comment_dislike_public_count',

                'group_followers' => 'group_follow_public_record',
                'hashtag_followers' => 'hashtag_follow_public_record',
                'post_followers' => 'post_follow_public_record',
                'comment_followers' => 'comment_follow_public_record',
                'group_follower_count' => 'group_follow_public_count',
                'hashtag_follower_count' => 'hashtag_follow_public_count',
                'post_follower_count' => 'post_follow_public_count',
                'comment_follower_count' => 'comment_follow_public_count',

                'group_blockers' => 'group_block_public_record',
                'hashtag_blockers' => 'hashtag_block_public_record',
                'post_blockers' => 'post_block_public_record',
                'comment_blockers' => 'comment_block_public_record',
                'group_blocker_count' => 'group_block_public_count',
                'hashtag_blocker_count' => 'hashtag_block_public_count',
                'post_blocker_count' => 'post_block_public_count',
                'comment_blocker_count' => 'comment_block_public_count',
                default => null,
            };

            if (empty($newItemKey)) {
                continue;
            }

            $config->update([
                'item_key' => $newItemKey,
            ]);
        }

        $userConfigs = Config::whereIn('item_key', [
            'user_likers',
            'user_liker_count',
            'user_dislikers',
            'user_disliker_count',
            'user_followers',
            'user_follower_count',
            'user_blockers',
            'user_blocker_count',
        ])->get();

        foreach ($userConfigs as $userConfig) {
            $itemKey = $userConfig->item_key;

            $newItemKey = match ($itemKey) {
                'user_likers' => 'user_like_public_record',
                'user_liker_count' => 'user_like_public_count',
                'user_dislikers' => 'user_dislike_public_record',
                'user_disliker_count' => 'user_dislike_public_count',
                'user_followers' => 'user_follow_public_record',
                'user_follower_count' => 'user_follow_public_count',
                'user_blockers' => 'user_block_public_record',
                'user_blocker_count' => 'user_block_public_count',
                default => null,
            };

            if (empty($newItemKey)) {
                continue;
            }

            $userConfig->update([
                'item_key' => $newItemKey,
                'item_value' => '3',
                'item_type' => 'number',
            ]);
        }

        $langConfigs = Config::whereIn('item_key', [
            'like_user_name', // user_like_name
            'dislike_user_name', // user_dislike_name
            'follow_user_name', // user_follow_name
            'block_user_name', // user_block_name
            'user_follower_name', // user_follow_user_title

            'like_group_name',
            'dislike_group_name',
            'follow_group_name',
            'block_group_name',
            'group_follower_name',

            'like_hashtag_name',
            'dislike_hashtag_name',
            'follow_hashtag_name',
            'block_hashtag_name',
            'hashtag_follower_name',

            'like_post_name',
            'dislike_post_name',
            'follow_post_name',
            'block_post_name',
            'post_follower_name',

            'like_comment_name',
            'dislike_comment_name',
            'follow_comment_name',
            'block_comment_name',
            'comment_follower_name',
        ])->get();

        foreach ($langConfigs as $langConfig) {
            $itemKey = $langConfig->item_key;

            $newItemKey = match ($itemKey) {
                'like_user_name' => 'user_like_name',
                'dislike_user_name' => 'user_dislike_name',
                'follow_user_name' => 'user_follow_name',
                'block_user_name' => 'user_block_name',
                'user_follower_name' => 'user_follow_user_title',

                'like_group_name' => 'group_like_name',
                'dislike_group_name' => 'group_dislike_name',
                'follow_group_name' => 'group_follow_name',
                'block_group_name' => 'group_block_name',
                'group_follower_name' => 'group_follow_user_title',

                'like_hashtag_name' => 'hashtag_like_name',
                'dislike_hashtag_name' => 'hashtag_dislike_name',
                'follow_hashtag_name' => 'hashtag_follow_name',
                'block_hashtag_name' => 'hashtag_block_name',
                'hashtag_follower_name' => 'hashtag_follow_user_title',

                'like_post_name' => 'post_like_name',
                'dislike_post_name' => 'post_dislike_name',
                'follow_post_name' => 'post_follow_name',
                'block_post_name' => 'post_block_name',
                'post_follower_name' => 'post_follow_user_title',

                'like_comment_name' => 'comment_like_name',
                'dislike_comment_name' => 'comment_dislike_name',
                'follow_comment_name' => 'comment_follow_name',
                'block_comment_name' => 'comment_block_name',
                'comment_follower_name' => 'comment_follow_user_title',
                default => null,
            };

            if (empty($newItemKey)) {
                continue;
            }

            $languageItems = Language::where('table_name', 'configs')->where('table_key', $itemKey)->get();

            $langItemValue = null;
            foreach ($languageItems as $langItem) {
                $langItemValue[$langItem->lang_tag] = $langItem->lang_content;
            }

            $newValue = $langItemValue ? json_encode($langItemValue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK) : '{"en":"Untitled","zh-Hans":"未命名","zh-Hant":"未命名"}';

            $langConfig->update([
                'item_key' => $newItemKey,
                'item_value' => $newValue,
                'item_type' => 'object',
            ]);
        }

        ConfigUtility::addFresnsConfigItems([
            [
                'item_key' => 'user_like_user_title',
                'item_value' => '{"en":"Likers","zh-Hans":"喜欢者","zh-Hant":"喜歡者"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
            ],
            [
                'item_key' => 'user_dislike_user_title',
                'item_value' => '{"en":"Dislikers","zh-Hans":"不喜欢者","zh-Hant":"不喜歡者"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
            ],
            [
                'item_key' => 'user_block_user_title',
                'item_value' => '{"en":"Blockers","zh-Hans":"黑名单","zh-Hant":"黑名單"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
            ],
            [
                'item_key' => 'group_like_user_title',
                'item_value' => '{"en":"Likers","zh-Hans":"喜欢者","zh-Hant":"喜歡者"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
            ],
            [
                'item_key' => 'group_dislike_user_title',
                'item_value' => '{"en":"Dislikers","zh-Hans":"不喜欢者","zh-Hant":"不喜歡者"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
            ],
            [
                'item_key' => 'group_block_user_title',
                'item_value' => '{"en":"Blockers","zh-Hans":"屏蔽者","zh-Hant":"封鎖名單"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
            ],
            [
                'item_key' => 'hashtag_like_user_title',
                'item_value' => '{"en":"Likers","zh-Hans":"喜欢者","zh-Hant":"喜歡者"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
            ],
            [
                'item_key' => 'hashtag_dislike_user_title',
                'item_value' => '{"en":"Dislikers","zh-Hans":"不喜欢者","zh-Hant":"不喜歡者"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
            ],
            [
                'item_key' => 'hashtag_block_user_title',
                'item_value' => '{"en":"Blockers","zh-Hans":"屏蔽者","zh-Hant":"封鎖名單"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
            ],
            [
                'item_key' => 'post_like_user_title',
                'item_value' => '{"en":"Likers","zh-Hans":"喜欢者","zh-Hant":"喜歡者"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
            ],
            [
                'item_key' => 'post_dislike_user_title',
                'item_value' => '{"en":"Dislikers","zh-Hans":"不喜欢者","zh-Hant":"不喜歡者"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
            ],
            [
                'item_key' => 'post_block_user_title',
                'item_value' => '{"en":"Blockers","zh-Hans":"屏蔽者","zh-Hant":"封鎖名單"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
            ],
            [
                'item_key' => 'comment_like_user_title',
                'item_value' => '{"en":"Likers","zh-Hans":"喜欢者","zh-Hant":"喜歡者"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
            ],
            [
                'item_key' => 'comment_dislike_user_title',
                'item_value' => '{"en":"Dislikers","zh-Hans":"不喜欢者","zh-Hant":"不喜歡者"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
            ],
            [
                'item_key' => 'comment_block_user_title',
                'item_value' => '{"en":"Blockers","zh-Hans":"屏蔽者","zh-Hant":"封鎖名單"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
            ],
            [
                'item_key' => 'geotag_like_enabled',
                'item_value' => 'false',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'geotag_like_name',
                'item_value' => '{"en":"Like","zh-Hans":"赞","zh-Hant":"喜歡"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 0,
            ],
            [
                'item_key' => 'geotag_like_user_title',
                'item_value' => '{"en":"Likers","zh-Hans":"喜欢者","zh-Hant":"喜歡者"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 0,
            ],
            [
                'item_key' => 'geotag_like_public_record',
                'item_value' => 'false',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
            [
                'item_key' => 'geotag_like_public_count',
                'item_value' => 'true',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
            [
                'item_key' => 'geotag_dislike_enabled',
                'item_value' => 'false',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'geotag_dislike_name',
                'item_value' => '{"en":"Dislike","zh-Hans":"不感兴趣","zh-Hant":"不感興趣"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 0,
            ],
            [
                'item_key' => 'geotag_dislike_user_title',
                'item_value' => '{"en":"Dislikers","zh-Hans":"不喜欢者","zh-Hant":"不喜歡者"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 0,
            ],
            [
                'item_key' => 'geotag_dislike_public_record',
                'item_value' => 'false',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
            [
                'item_key' => 'geotag_dislike_public_count',
                'item_value' => 'true',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
            [
                'item_key' => 'geotag_follow_enabled',
                'item_value' => 'true',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'geotag_follow_name',
                'item_value' => '{"en":"Watching","zh-Hans":"订阅","zh-Hant":"訂閱"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 0,
            ],
            [
                'item_key' => 'geotag_follow_user_title',
                'item_value' => '{"en":"Subscribers","zh-Hans":"订阅者","zh-Hant":"訂閱者"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 0,
            ],
            [
                'item_key' => 'geotag_follow_public_record',
                'item_value' => 'false',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
            [
                'item_key' => 'geotag_follow_public_count',
                'item_value' => 'true',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
            [
                'item_key' => 'geotag_block_enabled',
                'item_value' => 'false',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'geotag_block_name',
                'item_value' => '{"en":"Block","zh-Hans":"屏蔽","zh-Hant":"封鎖"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 0,
            ],
            [
                'item_key' => 'geotag_block_user_title',
                'item_value' => '{"en":"Blockers","zh-Hans":"屏蔽者","zh-Hant":"封鎖名單"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 0,
            ],
            [
                'item_key' => 'geotag_block_public_record',
                'item_value' => 'false',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
            [
                'item_key' => 'geotag_block_public_count',
                'item_value' => 'false',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
        ]);

        $profile_default_homepage = Config::where('item_key', 'profile_default_homepage')->first();
        if ($profile_default_homepage) {
            $profile_default_homepage->item_value = 'posts';
            $profile_default_homepage->save();
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
