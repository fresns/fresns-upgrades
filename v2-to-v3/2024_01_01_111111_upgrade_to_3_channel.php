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
            'channel configs',
        ]);

        Config::whereIn('item_key', [
            'menu_portal_keywords',
            'menu_portal_description',
            'menu_user_keywords',
            'menu_user_description',
            'menu_user_list_keywords',
            'menu_user_list_description',
            'menu_group_keywords',
            'menu_group_description',
            'menu_group_list_keywords',
            'menu_group_list_description',
            'menu_hashtag_keywords',
            'menu_hashtag_description',
            'menu_hashtag_list_keywords',
            'menu_hashtag_list_description',
            'menu_post_keywords',
            'menu_post_description',
            'menu_post_list_keywords',
            'menu_post_list_description',
            'menu_comment_keywords',
            'menu_comment_description',
            'menu_comment_list_keywords',
            'menu_comment_list_description',
            'menu_location_posts',
            'menu_location_comments',
            'menu_account_register',
            'menu_account_login',
            'menu_account_reset_password',
        ])->forceDelete();

        // lang
        $langConfigs = Config::whereIn('item_key', [
            'menu_portal_name',
            'menu_user_name',
            'menu_user_list_name',
            'menu_like_users',
            'menu_dislike_users',
            'menu_follow_users',
            'menu_block_users',
            'menu_group_name',
            'menu_group_list_name',
            'menu_like_groups',
            'menu_dislike_groups',
            'menu_follow_groups',
            'menu_block_groups',
            'menu_hashtag_name',
            'menu_hashtag_list_name',
            'menu_like_hashtags',
            'menu_dislike_hashtags',
            'menu_follow_hashtags',
            'menu_block_hashtags',
            'menu_post_name',
            'menu_post_list_name',
            'menu_like_posts',
            'menu_dislike_posts',
            'menu_follow_posts',
            'menu_block_posts',
            'menu_comment_name',
            'menu_comment_list_name',
            'menu_like_comments',
            'menu_dislike_comments',
            'menu_follow_comments',
            'menu_block_comments',
            'menu_follow_all_posts',
            'menu_follow_user_posts',
            'menu_follow_group_posts',
            'menu_follow_hashtag_posts',
            'menu_follow_all_comments',
            'menu_follow_user_comments',
            'menu_follow_group_comments',
            'menu_follow_hashtag_comments',
            'menu_nearby_posts',
            'menu_nearby_comments',
            'menu_account',
            'menu_account_wallet',
            'menu_editor_drafts',
            'menu_editor_functions',
            'menu_account_users',
            'menu_account_settings',
            'menu_conversations',
            'menu_notifications',
            'menu_notifications_all',
            'menu_notifications_systems',
            'menu_notifications_recommends',
            'menu_notifications_likes',
            'menu_notifications_dislikes',
            'menu_notifications_follows',
            'menu_notifications_blocks',
            'menu_notifications_mentions',
            'menu_notifications_comments',
            'menu_notifications_quotes',
            'menu_search',
        ])->get();

        foreach ($langConfigs as $langConfig) {
            $itemKey = $langConfig->item_key;

            $newItemKey = match ($itemKey) {
                'menu_portal_name' => 'channel_portal_name',
                'menu_user_name' => 'channel_user_name',
                'menu_user_list_name' => 'channel_user_list_name',
                'menu_like_users' => 'channel_likes_users_name',
                'menu_dislike_users' => 'channel_dislikes_users_name',
                'menu_follow_users' => 'channel_following_users_name',
                'menu_block_users' => 'channel_blocking_users_name',
                'menu_group_name' => 'channel_group_name',
                'menu_group_list_name' => 'channel_group_list_name',
                'menu_like_groups' => 'channel_likes_groups_name',
                'menu_dislike_groups' => 'channel_dislikes_groups_name',
                'menu_follow_groups' => 'channel_following_groups_name',
                'menu_block_groups' => 'channel_blocking_groups_name',
                'menu_hashtag_name' => 'channel_hashtag_name',
                'menu_hashtag_list_name' => 'channel_hashtag_list_name',
                'menu_like_hashtags' => 'channel_likes_hashtags_name',
                'menu_dislike_hashtags' => 'channel_dislikes_hashtags_name',
                'menu_follow_hashtags' => 'channel_following_hashtags_name',
                'menu_block_hashtags' => 'channel_blocking_hashtags_name',
                'menu_post_name' => 'channel_post_name',
                'menu_post_list_name' => 'channel_post_list_name',
                'menu_like_posts' => 'channel_likes_posts_name',
                'menu_dislike_posts' => 'channel_dislikes_posts_name',
                'menu_follow_posts' => 'channel_following_posts_name',
                'menu_block_posts' => 'channel_blocking_posts_name',
                'menu_comment_name' => 'channel_comment_name',
                'menu_comment_list_name' => 'channel_comment_list_name',
                'menu_like_comments' => 'channel_likes_comments_name',
                'menu_dislike_comments' => 'channel_dislikes_comments_name',
                'menu_follow_comments' => 'channel_following_comments_name',
                'menu_block_comments' => 'channel_blocking_comments_name',
                'menu_follow_all_posts' => 'channel_timeline_posts_name',
                'menu_follow_user_posts' => 'channel_timeline_user_posts_name',
                'menu_follow_group_posts' => 'channel_timeline_group_posts_name',
                'menu_follow_hashtag_posts' => 'channel_timeline_name',
                'menu_follow_all_comments' => 'channel_timeline_comments_name',
                'menu_follow_user_comments' => 'channel_timeline_user_comments_name',
                'menu_follow_group_comments' => 'channel_timeline_group_comments_name',
                'menu_follow_hashtag_comments' => 'channel_nearby_name',
                'menu_nearby_posts' => 'channel_nearby_posts_name',
                'menu_nearby_comments' => 'channel_nearby_comments_name',
                'menu_account' => 'channel_me_name',
                'menu_account_wallet' => 'channel_me_wallet_name',
                'menu_editor_functions' => 'channel_me_extcredits_name',
                'menu_editor_drafts' => 'channel_me_drafts_name',
                'menu_account_users' => 'channel_me_users_name',
                'menu_account_settings' => 'channel_me_settings_name',
                'menu_conversations' => 'channel_conversations_name',
                'menu_notifications' => 'channel_notifications_name',
                'menu_notifications_all' => 'channel_notifications_all_name',
                'menu_notifications_systems' => 'channel_notifications_systems_name',
                'menu_notifications_recommends' => 'channel_notifications_recommends_name',
                'menu_notifications_likes' => 'channel_notifications_likes_name',
                'menu_notifications_dislikes' => 'channel_notifications_dislikes_name',
                'menu_notifications_follows' => 'channel_notifications_follows_name',
                'menu_notifications_blocks' => 'channel_notifications_blocks_name',
                'menu_notifications_mentions' => 'channel_notifications_mentions_name',
                'menu_notifications_comments' => 'channel_notifications_comments_name',
                'menu_notifications_quotes' => 'channel_notifications_quotes_name',
                'menu_search' => 'channel_search_name',
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

        // seo
        $seoLangConfigs = Config::whereIn('item_key', [
            'menu_portal_title',
            'menu_user_title',
            'menu_user_list_title',
            'menu_group_title',
            'menu_group_list_title',
            'menu_hashtag_title',
            'menu_hashtag_list_title',
            'menu_post_title',
            'menu_post_list_title',
            'menu_comment_title',
            'menu_comment_list_title',
        ])->get();

        foreach ($seoLangConfigs as $seoLangConfig) {
            $itemKey = $seoLangConfig->item_key;

            $newItemKey = match ($itemKey) {
                'menu_portal_title' => 'channel_portal_seo',
                'menu_user_title' => 'channel_user_seo',
                'menu_user_list_title' => 'channel_user_list_seo',
                'menu_group_title' => 'channel_group_seo',
                'menu_group_list_title' => 'channel_group_list_seo',
                'menu_hashtag_title' => 'channel_hashtag_seo',
                'menu_hashtag_list_title' => 'channel_hashtag_list_seo',
                'menu_post_title' => 'channel_post_seo',
                'menu_post_list_title' => 'channel_post_list_seo',
                'menu_comment_title' => 'channel_comment_seo',
                'menu_comment_list_title' => 'channel_comment_list_seo',
            };

            $seoLangConfig->update([
                'item_key' => $newItemKey,
                'item_value' => json_decode('{"en":{"title":"","description":"","keywords":""},"zh-Hans":{"title":"","description":"","keywords":""},"zh-Hant":{"title":"","description":"","keywords":""}}', true),
                'item_type' => 'object',
            ]);
        }

        // status
        $statusLangConfigs = Config::whereIn('item_key', [
            'menu_user_query_state',
            'menu_user_query_config',
            'menu_user_list_query_state',
            'menu_user_list_query_config',
            'menu_group_type',
            'menu_group_query_state',
            'menu_group_query_config',
            'menu_group_list_query_state',
            'menu_group_list_query_config',
            'menu_hashtag_query_state',
            'menu_hashtag_query_config',
            'menu_hashtag_list_query_state',
            'menu_hashtag_list_query_config',
            'menu_post_query_state',
            'menu_post_query_config',
            'menu_post_list_query_state',
            'menu_post_list_query_config',
            'menu_comment_query_state',
            'menu_comment_query_config',
            'menu_comment_list_query_state',
            'menu_comment_list_query_config',
        ])->get();

        foreach ($statusLangConfigs as $statusLangConfig) {
            $itemKey = $statusLangConfig->item_key;

            $newItemKey = match ($itemKey) {
                'menu_user_query_state' => 'channel_user_query_state',
                'menu_user_query_config' => 'channel_user_query_config',
                'menu_user_list_query_state' => 'channel_user_list_query_state',
                'menu_user_list_query_config' => 'channel_user_list_query_config',
                'menu_group_type' => 'channel_group_type',
                'menu_group_query_state' => 'channel_group_query_state',
                'menu_group_query_config' => 'channel_group_query_config',
                'menu_group_list_query_state' => 'channel_group_list_query_state',
                'menu_group_list_query_config' => 'channel_group_list_query_config',
                'menu_hashtag_query_state' => 'channel_hashtag_query_state',
                'menu_hashtag_query_config' => 'channel_hashtag_query_config',
                'menu_hashtag_list_query_state' => 'channel_hashtag_list_query_state',
                'menu_hashtag_list_query_config' => 'channel_hashtag_list_query_config',
                'menu_post_query_state' => 'channel_post_query_state',
                'menu_post_query_config' => 'channel_post_query_config',
                'menu_post_list_query_state' => 'channel_post_list_query_state',
                'menu_post_list_query_config' => 'channel_post_list_query_config',
                'menu_comment_query_state' => 'channel_comment_query_state',
                'menu_comment_query_config' => 'channel_comment_query_config',
                'menu_comment_list_query_state' => 'channel_comment_list_query_state',
                'menu_comment_list_query_config' => 'channel_comment_list_query_config',
            };

            $statusLangConfig->update([
                'item_key' => $newItemKey,
            ]);
        }

        $statusConfigs = Config::whereIn('item_key', [
            'menu_portal_status',
            'menu_user_status',
            'menu_user_list_status',
            'menu_group_status',
            'menu_group_list_status',
            'menu_hashtag_status',
            'menu_hashtag_list_status',
            'menu_post_status',
            'menu_post_list_status',
            'menu_comment_status',
            'menu_comment_list_status',
        ])->get();

        foreach ($statusConfigs as $config) {
            $itemKey = $config->item_key;

            $newItemKey = match ($itemKey) {
                'menu_portal_status' => 'channel_portal_status',
                'menu_user_status' => 'channel_user_status',
                'menu_user_list_status' => 'channel_user_list_status',
                'menu_group_status' => 'channel_group_status',
                'menu_group_list_status' => 'channel_group_list_status',
                'menu_hashtag_status' => 'channel_hashtag_status',
                'menu_hashtag_list_status' => 'channel_hashtag_list_status',
                'menu_post_status' => 'channel_post_status',
                'menu_post_list_status' => 'channel_post_list_status',
                'menu_comment_status' => 'channel_comment_status',
                'menu_comment_list_status' => 'channel_comment_list_status',
            };

            $config->update([
                'item_key' => $newItemKey,
            ]);
        }

        ConfigUtility::addFresnsConfigItems([
            [
                'item_key' => 'channel_geotag_name',
                'item_value' => '{"en":"Geotags","zh-Hans":"地理主页","zh-Hant":"地理首頁"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_geotag_seo',
                'item_value' => '{"en":{"title":"","description":"","keywords":""},"zh-Hans":{"title":"","description":"","keywords":""},"zh-Hant":{"title":"","description":"","keywords":""}}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_geotag_query_state',
                'item_value' => '2',
                'item_type' => 'number',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_geotag_query_config',
                'item_value' => null,
                'item_type' => 'string',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_geotag_status',
                'item_value' => 'true',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_geotag_list_name',
                'item_value' => '{"en":"Geotag List","zh-Hans":"地理列表","zh-Hant":"地理列表"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_geotag_list_seo',
                'item_value' => '{"en":{"title":"","description":"","keywords":""},"zh-Hans":{"title":"","description":"","keywords":""},"zh-Hant":{"title":"","description":"","keywords":""}}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_geotag_list_query_state',
                'item_value' => '2',
                'item_type' => 'number',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_geotag_list_query_config',
                'item_value' => null,
                'item_type' => 'string',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_geotag_list_status',
                'item_value' => 'true',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_geotag_detail_type',
                'item_value' => 'posts',
                'item_type' => 'string',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_likes_geotags_name',
                'item_value' => '{"en":"My Like","zh-Hans":"我的喜欢","zh-Hant":"我的喜歡"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_dislikes_geotags_name',
                'item_value' => '{"en":"My Dislike","zh-Hans":"我不喜欢的","zh-Hant":"我不喜歡的"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_following_geotags_name',
                'item_value' => '{"en":"Favorites","zh-Hans":"收藏夹","zh-Hant":"收藏夾"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_blocking_geotags_name',
                'item_value' => '{"en":"Blacklist","zh-Hans":"黑名单","zh-Hant":"我的封鎖"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_timeline_type',
                'item_value' => 'posts',
                'item_type' => 'string',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_nearby_type',
                'item_value' => 'posts',
                'item_type' => 'string',
                'is_multilingual' => 0,
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
