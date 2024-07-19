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

class UpgradeTo2185 extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v2.18.5
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 2.18.5"]);

        try {
            Schema::table('account_connects', function (Blueprint $table) {
                $table->unique(['account_id', 'connect_platform_id'], 'account_connect_platform');
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
}
