<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;
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
            'rename table: plugins',
        ]);

        Schema::rename('plugins', 'apps');
        Schema::rename('plugin_callbacks', 'app_callbacks');
        Schema::rename('plugin_usages', 'app_usages');
        Schema::rename('plugin_badges', 'app_badges');
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
};
