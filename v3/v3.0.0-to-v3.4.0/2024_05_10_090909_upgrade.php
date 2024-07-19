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
     * Upgrade to v3.1.3
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 3.1.3"]);

        Schema::table('account_connects', function (Blueprint $table) {
            $table->text('connect_token')->nullable()->change();
            $table->text('connect_refresh_token')->nullable()->change();
        });

        LanguagePack::updateOrCreate([
            'lang_key' => 'editorSelectAudience',
        ], [
            'lang_values' => [
                'en' => 'Choose audience',
                'zh-Hans' => '选择受众',
                'zh-Hant' => '選擇受眾',
            ],
            'is_custom' => 0,
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
