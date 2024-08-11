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
     * Upgrade to v3.4.1
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion();

        if (is_array($currentVersion)) {
            $currentVersion = $currentVersion['version'];
        }

        if (version_compare($currentVersion, '3.4.1', '>=')) {
            return;
        }

        info('Migration: ', ["{$currentVersion} >> 3.4.1"]);

        Schema::table('stickers', function (Blueprint $table) {
            switch (config('database.default')) {
                case 'pgsql':
                    $table->jsonb('name')->nullable()->change();
                    break;

                default:
                    $table->json('name')->nullable()->change();
            }
        });
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
};
