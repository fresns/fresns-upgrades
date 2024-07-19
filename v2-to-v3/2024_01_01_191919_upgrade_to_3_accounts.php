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
            'accounts',
        ]);

        if (! Schema::hasColumn('accounts', 'birthday')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->date('birthday')->nullable()->after('password');
            });
        }

        if (Schema::hasColumn('users', 'birthday')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('birthday');
            });
        }

        if (! Schema::hasColumn('accounts', 'fs_connected_id')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->string('fs_connected_token', 64)->nullable()->unique('fs_connected_token')->after('verify_log');
                $table->string('fs_connected_id', 26)->nullable()->unique('fs_connected_id')->after('verify_log');
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
