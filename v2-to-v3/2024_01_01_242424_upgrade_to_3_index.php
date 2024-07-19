<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Utilities\AppUtility;
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
            'index',
        ]);

        // user
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index('gender', 'user_gender');
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index('verified_status', 'user_verified_status');
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('user_extcredits_logs', function (Blueprint $table) {
                $table->index('type', 'extcredits_type');
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('user_likes', function (Blueprint $table) {
                $table->index('user_id', 'user_like_user_id');
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('user_follows', function (Blueprint $table) {
                $table->index('user_id', 'user_follow_user_id');
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('user_likes', function (Blueprint $table) {
                $table->index(['mark_type', 'like_type', 'like_id'], 'user_like_users'); // get mark users
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('user_follows', function (Blueprint $table) {
                $table->index(['mark_type', 'follow_type', 'follow_id'], 'user_follow_users'); // get mark users
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('user_likes', function (Blueprint $table) {
                $table->index(['user_id', 'mark_type', 'like_type'], 'user_like_contents'); // get mark contents
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('user_follows', function (Blueprint $table) {
                $table->index(['user_id', 'mark_type', 'follow_type'], 'user_follow_contents'); // get mark contents
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('user_likes', function (Blueprint $table) {
                $table->unique(['user_id', 'like_type', 'like_id'], 'user_like_id'); // unique
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('user_follows', function (Blueprint $table) {
                $table->unique(['user_id', 'follow_type', 'follow_id'], 'user_follow_id'); // unique
            });
        } catch (\Exception $e) {}

        // is_enabled
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index('is_enabled', 'user_is_enabled');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('groups', function (Blueprint $table) {
                $table->index('is_enabled', 'group_is_enabled');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('hashtags', function (Blueprint $table) {
                $table->index('is_enabled', 'hashtag_is_enabled');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('geotags', function (Blueprint $table) {
                $table->index('is_enabled', 'geotag_is_enabled');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('posts', function (Blueprint $table) {
                $table->index('is_enabled', 'post_is_enabled');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('comments', function (Blueprint $table) {
                $table->index('is_enabled', 'comment_is_enabled');
            });
        } catch (\Exception $e) {}

        // is_anonymous and lang_tag
        try {
            Schema::table('posts', function (Blueprint $table) {
                $table->index('is_anonymous', 'post_is_anonymous');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('posts', function (Blueprint $table) {
                $table->index('lang_tag', 'post_lang_tag');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('comments', function (Blueprint $table) {
                $table->index('is_anonymous', 'comment_is_anonymous');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('comments', function (Blueprint $table) {
                $table->index('lang_tag', 'comment_lang_tag');
            });
        } catch (\Exception $e) {}

        // log_state
        try {
            Schema::table('post_logs', function (Blueprint $table) {
                $table->index('state', 'post_log_state');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('comment_logs', function (Blueprint $table) {
                $table->index('state', 'comment_log_state');
            });
        } catch (\Exception $e) {}

        // groups
        try {
            Schema::table('groups', function (Blueprint $table) {
                $table->index('privacy', 'group_privacy');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('groups', function (Blueprint $table) {
                $table->index('visibility', 'group_visibility');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('groups', function (Blueprint $table) {
                $table->index('is_recommend', 'group_is_recommend');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('group_admins', function (Blueprint $table) {
                $table->index('user_id', 'group_admin_user_id');
            });
        } catch (\Exception $e) {}

        // hashtags
        try {
            Schema::table('hashtags', function (Blueprint $table) {
                $table->index('type', 'hashtag_type');
            });
        } catch (\Exception $e) {}

        // messages
        try {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index('type', 'notification_type');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index('user_id', 'notification_user_id');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('conversations', function (Blueprint $table) {
                $table->index('a_user_id', 'conversation_a_user_id');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('conversations', function (Blueprint $table) {
                $table->index('b_user_id', 'conversation_b_user_id');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('conversation_messages', function (Blueprint $table) {
                $table->index('conversation_id', 'conversation_message_conversation_id');
            });
        } catch (\Exception $e) {}

        // archives
        try {
            Schema::table('archives', function (Blueprint $table) {
                $table->index('usage_type', 'archive_usage_type');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('archives', function (Blueprint $table) {
                $table->index('usage_group_id', 'archive_usage_group_id');
            });
        } catch (\Exception $e) {}

        // operations
        try {
            Schema::table('operations', function (Blueprint $table) {
                $table->index('code', 'operation_code');
            });
        } catch (\Exception $e) {}
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
};
