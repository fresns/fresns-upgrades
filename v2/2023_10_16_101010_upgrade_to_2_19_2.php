<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\AccountConnect;
use App\Models\Sticker;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v2.19.2
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 2.19.2"]);

        AccountConnect::where('connect_platform_id', 29)->update([
            'connect_platform_id' => AccountConnect::CONNECT_QQ_WEBSITE_APPLICATION,
        ]);

        Sticker::where('image_file_url', 'LIKE', '%assets/plugins%')->update([
            'image_file_url' => DB::raw("REPLACE(image_file_url, 'assets/plugins', 'assets')")
        ]);
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
};
