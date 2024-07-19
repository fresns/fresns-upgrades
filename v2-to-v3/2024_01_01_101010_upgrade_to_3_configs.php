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
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            'configs',
        ]);

        if (Schema::hasColumn('configs', 'item_tag')) {
            Schema::table('configs', function (Blueprint $table) {
                $table->dropColumn('item_tag');
            });
        }

        if (Schema::hasColumn('plugin_usages', 'name')) {
            Schema::table('plugin_usages', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }

        $panelPath = Config::where('item_key', 'panel_path')->first();
        if ($panelPath) {
            $panelPath->item_key = 'panel_configs';
            $panelPath->item_value = json_decode('{"path":"admin","port":null,"ipv4":[],"ipv6":[],"accountSupport":{"email":true,"phone":true,"aid":true}}', true);
            $panelPath->item_type = 'object';
            $panelPath->is_multilingual = 0;
            $panelPath->is_custom = 0;
            $panelPath->is_api = 0;
            $panelPath->save();
        }

        Config::whereIn('item_key', [
            'panel_port',
            'panel_auth',
            'storages',
            'language_pack',
            'language_pack_contents',
            'account_ip_location_status',
            'my_likers',
            'my_dislikers',
            'my_followers',
            'my_blockers',
        ])->forceDelete();

        $configs = Config::whereIn('item_key', [
            'install_datetime',
            'developer_mode',
            'site_copyright',
            'site_public_status',
            'site_public_service',
            'site_email_register',
            'site_phone_register',
            'site_email_login',
            'site_phone_login',
            'site_login_or_register',
            'account_cookies_status',
            'account_real_name_service',
            'wallet_withdraw_verify',
            'ban_names',
            'view_posts_by_follow_object',
            'view_comments_by_follow_object',
            'it_home_list',
            'preview_post_comment_sort',
            'preview_post_comment_require',
            'preview_sub_comments',
            'preview_sub_comment_sort',
            'post_editor_brief_length',
            'comment_editor_brief_length',
            'post_email_verify',
            'post_phone_verify',
            'post_real_name_verify',
            'post_editor_image_upload_form',
            'post_editor_video_upload_form',
            'post_editor_audio_upload_form',
            'post_editor_document_upload_form',
            'comment_email_verify',
            'comment_phone_verify',
            'comment_real_name_verify',
            'comment_editor_image_upload_form',
            'comment_editor_video_upload_form',
            'comment_editor_audio_upload_form',
            'comment_editor_document_upload_form',
            'post_follow_service',
            'comment_follow_service',
        ])->get();

        foreach ($configs as $config) {
            $itemKey = $config->item_key;

            $newItemKey = match ($itemKey) {
                'install_datetime' => 'installed_datetime',
                'developer_mode' => 'developer_configs',
                'site_copyright' => 'site_copyright_name',
                'site_public_status' => 'account_register_status', // private
                'site_public_service' => 'account_register_service', // private
                'site_email_register' => 'account_email_register', // private
                'site_phone_register' => 'account_phone_register', // private
                'site_email_login' => 'account_email_login', // private
                'site_phone_login' => 'account_phone_login', // private
                'site_login_or_register' => 'account_login_or_register', // private
                'account_cookies_status' => 'account_cookie_status',
                'account_real_name_service' => 'account_kyc_service',
                'wallet_withdraw_verify' => 'wallet_withdraw_check_kyc',
                'ban_names' => 'user_ban_names',
                'view_posts_by_follow_object' => 'channel_timeline_posts_status',
                'view_comments_by_follow_object' => 'channel_timeline_comments_status',
                'it_home_list' => 'profile_default_homepage',
                'preview_post_comment_sort' => 'preview_post_comments_type', // private
                'preview_post_comment_require' => 'preview_post_comments_threshold', // private
                'preview_sub_comments' => 'preview_comment_replies', // private
                'preview_sub_comment_sort' => 'preview_comment_replies_type', // private
                'post_editor_brief_length' => 'post_brief_length', // private
                'comment_editor_brief_length' => 'comment_brief_length', // private
                'post_email_verify' => 'post_required_email', // private
                'post_phone_verify' => 'post_required_phone', // private
                'post_real_name_verify' => 'post_required_kyc', // private
                'post_editor_image_upload_form' => 'post_editor_image_upload_type', // private
                'post_editor_video_upload_form' => 'post_editor_video_upload_type', // private
                'post_editor_audio_upload_form' => 'post_editor_audio_upload_type', // private
                'post_editor_document_upload_form' => 'post_editor_document_upload_type', // private
                'comment_email_verify' => 'comment_required_email', // private
                'comment_phone_verify' => 'comment_required_phone', // private
                'comment_real_name_verify' => 'comment_required_kyc', // private
                'comment_editor_image_upload_form' => 'comment_editor_image_upload_type', // private
                'comment_editor_video_upload_form' => 'comment_editor_video_upload_type', // private
                'comment_editor_audio_upload_form' => 'comment_editor_audio_upload_type', // private
                'comment_editor_document_upload_form' => 'comment_editor_document_upload_type', // private
                'post_follow_service' => 'post_timelines_service', // private
                'comment_follow_service' => 'comment_timelines_service', // private
                default => $itemKey,
            };

            $config->update([
                'item_key' => $newItemKey,
            ]);
        }

        $multi_user_service = Config::where('item_key', 'multi_user_service')->first();
        if ($multi_user_service) {
            $multi_user_service->item_key = 'account_users_service';
            $multi_user_service->item_value = null;
            $multi_user_service->item_type = 'plugin';
            $multi_user_service->is_multilingual = 0;
            $multi_user_service->is_custom = 0;
            $multi_user_service->is_api = 1;
            $multi_user_service->save();
        }

        $post_editor_title_view = Config::where('item_key', 'post_editor_title_view')->first();
        if ($post_editor_title_view) {
            $post_editor_title_view->item_key = 'post_editor_title_show';
            $post_editor_title_view->item_value = 'false';
            $post_editor_title_view->item_type = 'boolean';
            $post_editor_title_view->is_multilingual = 0;
            $post_editor_title_view->is_custom = 0;
            $post_editor_title_view->is_api = 0;
            $post_editor_title_view->save();
        }

        $langConfigs = Config::whereIn('item_key', [
            'account_terms',
            'account_privacy',
            'account_cookies',
            'account_delete',
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
            'group_name',
            'hashtag_name',
            'post_name',
            'comment_name',
            'publish_post_name',
            'publish_comment_name',
            'post_limit_tip',
            'comment_limit_tip',
        ])->get();

        foreach ($langConfigs as $langConfig) {
            $itemKey = $langConfig->item_key;

            $newItemKey = match ($itemKey) {
                'account_terms' => 'account_terms_policy',
                'account_privacy' => 'account_privacy_policy',
                'account_cookies' => 'account_cookie_policy',
                'account_delete' => 'account_delete_policy',
                default => $itemKey,
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
                'item_key' => 'image_filesystem_disk',
                'item_value' => 'local',
                'item_type' => 'string',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
            [
                'item_key' => 'video_filesystem_disk',
                'item_value' => 'local',
                'item_type' => 'string',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
            [
                'item_key' => 'audio_filesystem_disk',
                'item_value' => 'local',
                'item_type' => 'string',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
            [
                'item_key' => 'document_filesystem_disk',
                'item_value' => 'local',
                'item_type' => 'string',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
            [
                'item_key' => 'account_center_service',
                'item_value' => null,
                'item_type' => 'plugin',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'account_center_captcha',
                'item_value' => '{"type":"","siteKey":"","secretKey":""}',
                'item_type' => 'object',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
            [
                'item_key' => 'account_login_service',
                'item_value' => null,
                'item_type' => 'plugin',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'account_login_with_code',
                'item_value' => 'false',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
            [
                'item_key' => 'conversation_file_upload_type',
                'item_value' => '{"image":"api","video":"sdk","audio":"sdk","document":"sdk"}',
                'item_type' => 'object',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
            [
                'item_key' => 'geotag_name',
                'item_value' => '{"en":"Geotag","zh-Hans":"地理","zh-Hant":"地理"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 1,
            ],
            [
                'item_key' => 'mention_number',
                'item_value' => '0',
                'item_type' => 'number',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'hashtag_number',
                'item_value' => '0',
                'item_type' => 'number',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_nearby_posts_status',
                'item_value' => 'true',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_nearby_comments_status',
                'item_value' => 'false',
                'item_type' => 'boolean',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'preview_comment_like_users',
                'item_value' => '0',
                'item_type' => 'number',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
            [
                'item_key' => 'language_pack_version',
                'item_value' => '1.0.0',
                'item_type' => 'string',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'interface_command_words',
                'item_value' => null,
                'item_type' => 'array',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
            [
                'item_key' => 'search_geotags_service',
                'item_value' => null,
                'item_type' => 'plugin',
                'is_multilingual' => 0,
                'is_api' => 0,
            ],
            [
                'item_key' => 'channel_group_detail_type',
                'item_value' => 'posts',
                'item_type' => 'string',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_hashtag_detail_type',
                'item_value' => 'posts',
                'item_type' => 'string',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
        ]);

        Schema::dropIfExists('block_words');
        Schema::dropIfExists('placements');
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
};
