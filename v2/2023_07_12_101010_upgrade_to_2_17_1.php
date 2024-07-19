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

class UpgradeTo2171 extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v2.17.1
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 2.17.1"]);

        if (Schema::hasColumn('groups', 'description')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->text('description')->nullable()->change();
            });
        }

        if (Schema::hasColumn('hashtags', 'description')) {
            Schema::table('hashtags', function (Blueprint $table) {
                $table->text('description')->nullable()->change();
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
