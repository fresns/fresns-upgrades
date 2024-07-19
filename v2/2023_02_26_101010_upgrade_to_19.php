<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Helpers\CacheHelper;
use App\Models\Config;
use App\Models\Language;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpgradeTo19 extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to 19 (fresns v2.7.0)
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["2.7.0 => {$currentVersion}"]);

        if (version_compare('2.7.0', $currentVersion) == -1) {
            return;
        }

        if (! Schema::hasColumn('archives', 'usage_group_id')) {
            Schema::table('archives', function (Blueprint $table) {
                $table->unsignedInteger('usage_group_id')->default(0)->after('usage_type');
                $table->unsignedTinyInteger('usage_group_content_type')->nullable()->after('usage_type');
            });
        }

        if (Schema::hasColumn('archives', 'api_type')) {
            Schema::table('archives', function (Blueprint $table) {
                $table->renameColumn('api_type', 'value_type');
            });
        }

        // bulletin_name
        $bulletinName = Config::where('item_key', 'system_bulletin_name')->first();
        $bulletinName?->update([
            'item_key' => 'bulletin_name',
            'item_tag' => 'commons',
        ]);

        // bulletin_contents
        $bulletinContents = Config::where('item_key', 'system_bulletin_contents')->first();
        $bulletinContents?->update([
            'item_key' => 'bulletin_contents',
            'item_tag' => 'commons',
        ]);

        // advertising
        $advertising = Config::where('item_key', 'system_url')->first();
        try {
            if ($advertising) {
                $advertising->item_key = 'advertising';
                $advertising->item_value = null;
                $advertising->item_type = 'array';
                $advertising->item_tag = 'commons';
                $advertising->is_multilingual = 0;
                $advertising->is_custom = 0;
                $advertising->is_api = 1;
                $advertising->save();
            } else {
                $newConfig = new Config;
                $newConfig->item_key = 'advertising';
                $newConfig->item_value = null;
                $newConfig->item_type = 'array';
                $newConfig->item_tag = 'commons';
                $newConfig->is_multilingual = 0;
                $newConfig->is_custom = 0;
                $newConfig->is_api = 1;
                $newConfig->save();
            }
        } catch (\Exception $e) {}

        $langKeyNameArr = Language::where('table_key', 'system_bulletin_name')->get();
        foreach ($langKeyNameArr as $nameKey) {
            $nameKey->update([
                'table_key' => 'bulletin_name',
            ]);
        }

        $langKeyContentArr = Language::where('table_key', 'system_bulletin_contents')->get();
        foreach ($langKeyContentArr as $contentKey) {
            $contentKey->update([
                'table_key' => 'bulletin_contents',
            ]);
        }

        CacheHelper::clearAllCache();
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
}
