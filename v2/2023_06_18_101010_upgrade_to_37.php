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

class UpgradeTo37 extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v2.16.0
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 2.16.0"]);

        if (Schema::hasColumn('plugin_callbacks', 'is_use')) {
            Schema::table('plugin_callbacks', function (Blueprint $table) {
                $table->renameColumn('is_use', 'is_used');
            });
        }

        if (Schema::hasColumn('plugin_callbacks', 'use_plugin_fskey')) {
            Schema::table('plugin_callbacks', function (Blueprint $table) {
                $table->renameColumn('use_plugin_fskey', 'used_plugin_fskey');
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
}
