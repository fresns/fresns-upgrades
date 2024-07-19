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
            'profile configs',
        ]);

        $configs = Config::whereIn('item_key', [
            'it_posts',
            'it_comments',
            'it_followers_you_follow',

            'it_like_users',
            'it_like_groups',
            'it_like_hashtags',
            'it_like_posts',
            'it_like_comments',

            'it_dislike_users',
            'it_dislike_groups',
            'it_dislike_hashtags',
            'it_dislike_posts',
            'it_dislike_comments',

            'it_follow_users',
            'it_follow_groups',
            'it_follow_hashtags',
            'it_follow_posts',
            'it_follow_comments',

            'it_block_users',
            'it_block_groups',
            'it_block_hashtags',
            'it_block_posts',
            'it_block_comments',
        ])->get();

        foreach ($configs as $config) {
            $itemKey = $config->item_key;

            $newItemKey = match ($itemKey) {
                'it_posts' => 'profile_posts_enabled',
                'it_comments' => 'profile_comments_enabled',
                'it_followers_you_follow' => 'profile_followers_you_follow_enabled',

                'it_like_users' => 'profile_likes_users_enabled',
                'it_like_groups' => 'profile_likes_groups_enabled',
                'it_like_hashtags' => 'profile_likes_hashtags_enabled',
                'it_like_posts' => 'profile_likes_posts_enabled',
                'it_like_comments' => 'profile_likes_comments_enabled',

                'it_dislike_users' => 'profile_dislikes_users_enabled',
                'it_dislike_groups' => 'profile_dislikes_groups_enabled',
                'it_dislike_hashtags' => 'profile_dislikes_hashtags_enabled',
                'it_dislike_posts' => 'profile_dislikes_posts_enabled',
                'it_dislike_comments' => 'profile_dislikes_comments_enabled',

                'it_follow_users' => 'profile_following_users_enabled',
                'it_follow_groups' => 'profile_following_groups_enabled',
                'it_follow_hashtags' => 'profile_following_hashtags_enabled',
                'it_follow_posts' => 'profile_following_posts_enabled',
                'it_follow_comments' => 'profile_following_comments_enabled',

                'it_block_users' => 'profile_blocking_users_enabled',
                'it_block_groups' => 'profile_blocking_groups_enabled',
                'it_block_hashtags' => 'profile_blocking_hashtags_enabled',
                'it_block_posts' => 'profile_blocking_posts_enabled',
                'it_block_comments' => 'profile_blocking_comments_enabled',
            };

            $config->update([
                'item_key' => $newItemKey,
            ]);
        }

        $langConfigs = Config::whereIn('item_key', [
            'menu_profile_likes',
            'menu_profile_dislikes',
            'menu_profile_followers',
            'menu_profile_blockers',
            'menu_profile_followers_you_follow',

            'menu_profile_like_users',
            'menu_profile_like_groups',
            'menu_profile_like_hashtags',
            'menu_profile_like_posts',
            'menu_profile_like_comments',

            'menu_profile_dislike_users',
            'menu_profile_dislike_groups',
            'menu_profile_dislike_hashtags',
            'menu_profile_dislike_posts',
            'menu_profile_dislike_comments',

            'menu_profile_follow_users',
            'menu_profile_follow_groups',
            'menu_profile_follow_hashtags',
            'menu_profile_follow_posts',
            'menu_profile_follow_comments',

            'menu_profile_block_users',
            'menu_profile_block_groups',
            'menu_profile_block_hashtags',
            'menu_profile_block_posts',
            'menu_profile_block_comments',
        ])->get();

        foreach ($langConfigs as $langConfig) {
            $itemKey = $langConfig->item_key;

            $newItemKey = match ($itemKey) {
                'menu_profile_likes' => 'profile_likers_name',
                'menu_profile_dislikes' => 'profile_dislikers_name',
                'menu_profile_followers' => 'profile_followers_name',
                'menu_profile_blockers' => 'profile_blockers_name',
                'menu_profile_followers_you_follow' => 'profile_followers_you_follow_name',

                'menu_profile_like_users' => 'profile_likes_users_name',
                'menu_profile_like_groups' => 'profile_likes_groups_name',
                'menu_profile_like_hashtags' => 'profile_likes_hashtags_name',
                'menu_profile_like_posts' => 'profile_likes_posts_name',
                'menu_profile_like_comments' => 'profile_likes_comments_name',

                'menu_profile_dislike_users' => 'profile_dislikes_users_name',
                'menu_profile_dislike_groups' => 'profile_dislikes_groups_name',
                'menu_profile_dislike_hashtags' => 'profile_dislikes_hashtags_name',
                'menu_profile_dislike_posts' => 'profile_dislikes_posts_name',
                'menu_profile_dislike_comments' => 'profile_dislikes_comments_name',

                'menu_profile_follow_users' => 'profile_following_users_name',
                'menu_profile_follow_groups' => 'profile_following_groups_name',
                'menu_profile_follow_hashtags' => 'profile_following_hashtags_name',
                'menu_profile_follow_posts' => 'profile_following_posts_name',
                'menu_profile_follow_comments' => 'profile_following_comments_name',

                'menu_profile_block_users' => 'profile_blocking_users_name',
                'menu_profile_block_groups' => 'profile_blocking_groups_name',
                'menu_profile_block_hashtags' => 'profile_blocking_hashtags_name',
                'menu_profile_block_posts' => 'profile_blocking_posts_name',
                'menu_profile_block_comments' => 'profile_blocking_comments_name',
            };

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
                'item_key' => 'profile_likes_geotags_enabled',
                'item_value' => 'false',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'profile_likes_geotags_name',
                'item_value' => '{"en":"Like","zh-Hans":"赞","zh-Hant":"喜歡"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 1,
            ],
            [
                'item_key' => 'profile_dislikes_geotags_enabled',
                'item_value' => 'false',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'profile_dislikes_geotags_name',
                'item_value' => '{"en":"Dislike","zh-Hans":"不感兴趣","zh-Hant":"不感興趣"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 0,
            ],
            [
                'item_key' => 'profile_following_geotags_enabled',
                'item_value' => 'true',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'profile_following_geotags_name',
                'item_value' => '{"en":"Watching","zh-Hans":"订阅","zh-Hant":"訂閱"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 1,
            ],
            [
                'item_key' => 'profile_blocking_geotags_enabled',
                'item_value' => 'false',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'profile_blocking_geotags_name',
                'item_value' => '{"en":"Block","zh-Hans":"屏蔽","zh-Hant":"封鎖"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 1,
            ],
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
