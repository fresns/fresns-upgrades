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
            'plugin_fskey',
        ]);

        if (Schema::hasColumn('apps', 'is_standalone')) {
            Schema::table('apps', function (Blueprint $table) {
                $table->dropColumn('is_standalone');
            });
        }

        // apps
        if (Schema::hasColumn('app_callbacks', 'plugin_fskey')) {
            Schema::table('app_callbacks', function (Blueprint $table) {
                $table->renameColumn('plugin_fskey', 'app_fskey');
                $table->renameColumn('used_plugin_fskey', 'used_app_fskey');
            });
        }
        if (Schema::hasColumn('app_usages', 'plugin_fskey')) {
            Schema::table('app_usages', function (Blueprint $table) {
                $table->renameColumn('plugin_fskey', 'app_fskey');
            });
        }
        if (Schema::hasColumn('app_badges', 'plugin_fskey')) {
            Schema::table('app_badges', function (Blueprint $table) {
                $table->renameColumn('plugin_fskey', 'app_fskey');
            });
        }

        // accounts
        if (Schema::hasColumn('accounts', 'verify_plugin_fskey')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->renameColumn('verify_plugin_fskey', 'verify_app_fskey');
            });
        }
        if (Schema::hasColumn('account_connects', 'plugin_fskey')) {
            Schema::table('account_connects', function (Blueprint $table) {
                $table->renameColumn('plugin_fskey', 'app_fskey');
            });
        }
        if (Schema::hasColumn('account_wallet_logs', 'plugin_fskey')) {
            Schema::table('account_wallet_logs', function (Blueprint $table) {
                $table->renameColumn('plugin_fskey', 'app_fskey');
            });
        }

        // users
        if (Schema::hasColumn('user_extcredits_logs', 'plugin_fskey')) {
            Schema::table('user_extcredits_logs', function (Blueprint $table) {
                $table->renameColumn('plugin_fskey', 'app_fskey');
            });
        }

        // notifications
        if (Schema::hasColumn('notifications', 'plugin_fskey')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->renameColumn('is_access_plugin', 'is_access_app');
                $table->renameColumn('plugin_fskey', 'app_fskey');
            });
        }

        // code messages
        if (Schema::hasColumn('code_messages', 'plugin_fskey')) {
            Schema::table('code_messages', function (Blueprint $table) {
                $table->renameColumn('plugin_fskey', 'app_fskey');
            });
        }

        // file
        if (Schema::hasColumn('file_downloads', 'plugin_fskey')) {
            Schema::table('file_downloads', function (Blueprint $table) {
                $table->renameColumn('plugin_fskey', 'app_fskey');
            });
        }

        // group
        if (Schema::hasColumn('groups', 'plugin_fskey')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->renameColumn('plugin_fskey', 'follow_app_fskey');
            });
        }

        // post
        if (Schema::hasColumn('post_users', 'plugin_fskey')) {
            Schema::table('post_users', function (Blueprint $table) {
                $table->renameColumn('plugin_fskey', 'app_fskey');
            });
        }

        // archives
        if (Schema::hasColumn('archives', 'plugin_fskey')) {
            Schema::table('archives', function (Blueprint $table) {
                $table->renameColumn('plugin_fskey', 'app_fskey');
            });
        }
        if (Schema::hasColumn('archive_usages', 'plugin_fskey')) {
            Schema::table('archive_usages', function (Blueprint $table) {
                $table->renameColumn('plugin_fskey', 'app_fskey');
            });
        }

        // operations
        if (Schema::hasColumn('operations', 'plugin_fskey')) {
            Schema::table('operations', function (Blueprint $table) {
                $table->renameColumn('plugin_fskey', 'app_fskey');
            });
        }
        if (Schema::hasColumn('operation_usages', 'plugin_fskey')) {
            Schema::table('operation_usages', function (Blueprint $table) {
                $table->renameColumn('plugin_fskey', 'app_fskey');
            });
        }

        // extends
        if (Schema::hasColumn('extends', 'plugin_fskey')) {
            Schema::table('extends', function (Blueprint $table) {
                $table->renameColumn('plugin_fskey', 'app_fskey');
            });
        }
        if (Schema::hasColumn('extend_usages', 'plugin_fskey')) {
            Schema::table('extend_usages', function (Blueprint $table) {
                $table->renameColumn('plugin_fskey', 'app_fskey');
            });
        }

        // session
        if (Schema::hasColumn('session_keys', 'plugin_fskey')) {
            Schema::table('session_keys', function (Blueprint $table) {
                $table->renameColumn('app_secret', 'app_key');
                $table->renameColumn('plugin_fskey', 'app_fskey');
            });
        }
        if (Schema::hasColumn('session_logs', 'plugin_fskey')) {
            Schema::table('session_logs', function (Blueprint $table) {
                $table->renameColumn('plugin_fskey', 'app_fskey');
            });
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
