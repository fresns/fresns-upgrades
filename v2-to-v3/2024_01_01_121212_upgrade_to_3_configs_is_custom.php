<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\Config;
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
            'is_custom configs',
        ]);

        $configs = Config::whereIn('item_key', [
            'site_intro',
            'account_center_service',
            'account_center_captcha',
            'account_login_service',
            'account_login_with_code',
            'conversation_file_upload_type',
            'geotag_name',
            'mention_number',
            'hashtag_number',
            'channel_nearby_posts_status',
            'channel_nearby_posts_status',
            'preview_comment_like_users',
            'image_secret_app',
            'video_secret_app',
            'audio_secret_app',
            'document_secret_app',
            'image_filesystem_disk',
            'video_filesystem_disk',
            'audio_filesystem_disk',
            'document_filesystem_disk',
            'post_delete',
            'post_delete_sticky_limit',
            'post_delete_digest_limit',
            'comment_delete',
            'comment_delete_sticky_limit',
            'comment_delete_digest_limit',
            'user_like_user_title',
            'user_dislike_user_title',
            'user_follow_user_title',
            'user_block_user_title',
            'group_like_user_title',
            'group_dislike_user_title',
            'group_follow_user_title',
            'group_block_user_title',
            'hashtag_like_user_title',
            'hashtag_dislike_user_title',
            'hashtag_follow_user_title',
            'hashtag_block_user_title',
            'post_like_user_title',
            'post_dislike_user_title',
            'post_follow_user_title',
            'post_block_user_title',
            'comment_like_user_title',
            'comment_dislike_user_title',
            'comment_follow_user_title',
            'comment_block_user_title',
            'geotag_like_enabled',
            'geotag_like_name',
            'geotag_like_user_title',
            'geotag_like_public_record',
            'geotag_like_public_count',
            'geotag_dislike_enabled',
            'geotag_dislike_name',
            'geotag_dislike_user_title',
            'geotag_dislike_public_record',
            'geotag_dislike_public_count',
            'geotag_follow_enabled',
            'geotag_follow_name',
            'geotag_follow_user_title',
            'geotag_follow_public_record',
            'geotag_follow_public_count',
            'geotag_block_enabled',
            'geotag_block_name',
            'geotag_block_user_title',
            'geotag_block_public_record',
            'geotag_block_public_count',
            'profile_likes_geotags_enabled',
            'profile_likes_geotags_name',
            'profile_dislikes_geotags_enabled',
            'profile_dislikes_geotags_name',
            'profile_following_geotags_enabled',
            'profile_following_geotags_name',
            'profile_blocking_geotags_enabled',
            'profile_blocking_geotags_name',
            'channel_geotag_name',
            'channel_geotag_seo',
            'channel_geotag_query_state',
            'channel_geotag_query_config',
            'channel_geotag_status',
            'channel_geotag_list_name',
            'channel_geotag_list_seo',
            'channel_geotag_list_query_state',
            'channel_geotag_list_query_config',
            'channel_geotag_list_status',
            'channel_likes_geotags_name',
            'channel_dislikes_geotags_name',
            'channel_following_geotags_name',
            'channel_blocking_geotags_name',
            'channel_timeline_type',
            'channel_nearby_type',
            'website_geotag_path',
            'website_geotag_detail_path',
            'language_pack_version',
            'interface_command_words',
            'search_geotags_service',
            'post_edit',
            'image_filesystem_disk',
            'video_filesystem_disk',
            'audio_filesystem_disk',
            'document_filesystem_disk',
        ])->get();

        foreach ($configs as $config) {
            $config->update([
                'is_custom' => false,
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
