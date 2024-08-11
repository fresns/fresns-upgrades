<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\LanguagePack;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v3.6.0
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion();

        if (is_array($currentVersion)) {
            $currentVersion = $currentVersion['version'];
        }

        if (version_compare($currentVersion, '3.6.0', '>=')) {
            return;
        }

        info('Migration: ', ["{$currentVersion} >> 3.6.0"]);

        $contentHotList = LanguagePack::where('lang_key', 'contentHotList')->first();
        if ($contentHotList) {
            $contentHotList->update([
                'lang_key' => 'contentPopularList',
            ]);
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
