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

class UpgradeTo36a extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v2.15.0
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 2.15.0"]);

        if (Schema::hasColumn('account_connects', 'connect_account_id')) {
            return;
        }

        if (! Schema::hasColumn('account_connects', 'connect_token')) {
            Schema::table('account_connects', function (Blueprint $table) {
                $table->string('connect_token', 128)->nullable()->after('connect_account_id');
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
