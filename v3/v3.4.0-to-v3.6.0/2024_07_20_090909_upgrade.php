<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\LanguagePack;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v3.5.1
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion();

        if (is_array($currentVersion)) {
            $currentVersion = $currentVersion['version'];
        }

        if (version_compare($currentVersion, '3.5.1', '>=')) {
            return;
        }

        info('Migration: ', ["{$currentVersion} >> 3.5.1"]);

        $countryCode = LanguagePack::where('lang_key', 'countryCode')->first();
        if ($countryCode) {
            $countryCode->update([
                'lang_key' => 'countryCallingCode',
            ]);
        }

        if (Schema::hasColumn('accounts', 'country_code')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->renameColumn('country_code', 'country_calling_code');
            });
        }

        if (Schema::hasColumn('accounts', 'pure_phone')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->dropColumn('pure_phone');
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
