<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\Config;
use App\Models\Language;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v2.21.2
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 2.21.2"]);

        if (! Schema::hasColumn('accounts', 'fs_connected_id')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->string('fs_connected_token', 64)->nullable()->unique('fs_connected_token')->after('verify_log');
                $table->string('fs_connected_id', 26)->nullable()->unique('fs_connected_id')->after('verify_log');
            });
        }

        if (! Schema::hasColumn('posts', 'digested_at')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->timestamp('digested_at')->nullable()->after('digest_state');
            });
        }

        if (! Schema::hasColumn('comments', 'digested_at')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->timestamp('digested_at')->nullable()->after('digest_state');
            });
        }

        // lang pack add key
        $languagePack = Config::where('item_key', 'language_pack')->first();
        if ($languagePack) {
            $packData = $languagePack->item_value;

            $addPackKeys = [
                [
                    'name' => 'contentDigestTime',
                    'canDelete' => false,
                ],
            ];

            // merge by name de-duplication
            $mergedData = $packData;
            foreach ($addPackKeys as $addPackKey) {
                $nameExists = false;
                foreach ($packData as $packItem) {
                    if ($packItem['name'] === $addPackKey['name']) {
                        $nameExists = true;
                        break;
                    }
                }

                if (! $nameExists) {
                    $mergedData[] = $addPackKey;
                }
            }

            $languagePack->item_value = $mergedData;
            $languagePack->save();
        }

        $langPackContents = Language::where('table_name', 'configs')->where('table_column', 'item_value')->where('table_key', 'language_pack_contents')->get();
        foreach ($langPackContents as $packContent) {
            $content = $packContent->lang_content;
            if (empty($content)) {
                continue;
            }

            $content = (object) json_decode($content, true);

            $langAddContent = match ($packContent->lang_tag) {
                'en' => [
                    'contentDigestTime' => 'Digest Time',
                ],
                'zh-Hans' => [
                    'contentDigestTime' => '精华时间',
                ],
                'zh-Hant' => [
                    'contentDigestTime' => '精華時間',
                ],
                default => null,
            };

            if (empty($langAddContent)) {
                continue;
            }

            // merge by key de-duplication
            $langNewContent = clone $content;
            foreach ($langAddContent as $key => $value) {
                if (!property_exists($content, $key)) {
                    $langNewContent->$key = $value;
                }
            }

            $packContent->lang_content = json_encode($langNewContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
            $packContent->save();
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
