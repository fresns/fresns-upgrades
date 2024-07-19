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
            'more_json',
        ]);

        if (Schema::hasColumn('session_logs', 'more_json')) {
            Schema::table('session_logs', function (Blueprint $table) {
                $table->renameColumn('more_json', 'more_info');
            });
        }

        if (Schema::hasColumn('account_connects', 'more_json')) {
            Schema::table('account_connects', function (Blueprint $table) {
                $table->renameColumn('more_json', 'more_info');
            });
        }

        if (Schema::hasColumn('account_wallet_logs', 'more_json')) {
            Schema::table('account_wallet_logs', function (Blueprint $table) {
                $table->renameColumn('more_json', 'more_info');
            });
        }

        if (Schema::hasColumn('roles', 'more_json')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->renameColumn('more_json', 'more_info');
            });
        }

        if (Schema::hasColumn('extends', 'more_json')) {
            Schema::table('extends', function (Blueprint $table) {
                $table->renameColumn('more_json', 'more_info');
            });
        }

        if (Schema::hasColumn('files', 'more_json')) {
            Schema::table('files', function (Blueprint $table) {
                $table->renameColumn('more_json', 'more_info');
            });
        }

        if (! Schema::hasColumn('hashtags', 'more_info')) {
            Schema::table('hashtags', function (Blueprint $table) {
                $table->json('more_info')->nullable()->after('cover_file_url');
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
