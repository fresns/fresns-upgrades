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
            'users',
        ]);

        if (Schema::hasColumn('users', 'password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('password', 'pin');
            });
        }

        if (Schema::hasColumn('user_stats', 'view_me_count')) {
            Schema::table('user_stats', function (Blueprint $table) {
                $table->renameColumn('view_me_count', 'view_count');
            });
        }

        if (Schema::hasColumn('user_stats', 'like_me_count')) {
            Schema::table('user_stats', function (Blueprint $table) {
                $table->renameColumn('like_me_count', 'liker_count');
                $table->renameColumn('dislike_me_count', 'disliker_count');
                $table->renameColumn('follow_me_count', 'follower_count');
                $table->renameColumn('block_me_count', 'blocker_count');
            });
        }

        if (Schema::hasColumn('users', 'conversation_limit')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('conversation_limit', 'conversation_policy');
                $table->renameColumn('comment_limit', 'comment_policy');
            });
        }

        if (! Schema::hasColumn('users', 'birthday_display_type')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedTinyInteger('birthday_display_type')->default(1)->after('gender');
            });
        }

        if (! Schema::hasColumn('users', 'gender_pronoun')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('gender_custom')->nullable()->after('gender');
                $table->unsignedTinyInteger('gender_pronoun')->nullable()->after('gender');
                $table->json('more_info')->nullable()->after('comment_policy');
            });
        }

        if (! Schema::hasColumn('users', 'last_login_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_login_at')->nullable()->after('expired_at');
            });
        }

        if (! Schema::hasTable('user_logs')) {
            Schema::create('user_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id')->index('user_log_user_id');
                $table->unsignedTinyInteger('type')->index('user_log_type');
                $table->text('content');
                $table->unsignedTinyInteger('is_enabled')->default(1);
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();
                $table->softDeletes();
            });
        }

        if (! Schema::hasColumn('user_stats', 'like_geotag_count')) {
            Schema::table('user_stats', function (Blueprint $table) {
                $table->unsignedInteger('like_geotag_count')->default(0)->after('like_hashtag_count');
                $table->unsignedInteger('dislike_geotag_count')->default(0)->after('dislike_hashtag_count');
                $table->unsignedInteger('follow_geotag_count')->default(0)->after('follow_hashtag_count');
                $table->unsignedInteger('block_geotag_count')->default(0)->after('block_hashtag_count');
            });
        }

        if (! Schema::hasColumn('user_follows', 'mark_type')) {
            Schema::table('user_follows', function (Blueprint $table) {
                $table->unsignedTinyInteger('mark_type')->default(1)->after('user_id');
            });
        }

        Schema::dropIfExists('user_blocks');
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
};
