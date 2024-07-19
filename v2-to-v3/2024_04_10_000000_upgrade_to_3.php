<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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
            'new',
        ]);

        Schema::dropIfExists('verify_codes');
        Schema::dropIfExists('app_callbacks');

        DB::table('code_messages')->truncate();
        DB::table('language_packs')->truncate();

        Artisan::call('db:seed', [
            '--class' => 'CodeMessagesTableSeeder',
            '--force' => true,
        ]);

        Artisan::call('db:seed', [
            '--class' => 'LanguagePacksTableSeeder',
            '--force' => true,
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
