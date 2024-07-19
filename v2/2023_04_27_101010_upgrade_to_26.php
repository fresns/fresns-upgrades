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
use App\Utilities\ArrUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UpgradeTo26 extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v2.10.1
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["2.10.1 => {$currentVersion}"]);

        if (version_compare('2.10.1', $currentVersion) == -1) {
            return;
        }

        if (Schema::hasColumn('post_appends', 'allow_proportion')) {
            Schema::table('post_appends', function (Blueprint $table) {
                $table->renameColumn('allow_proportion', 'allow_percentage');
            });
        }

        $website_proportion = Config::where('item_key', 'website_proportion')->first();
        if ($website_proportion) {
            $website_proportion->update([
                'item_key' => 'website_percentage',
            ]);
        }

        $languagePack = Config::where('item_key', 'language_pack')->first();
        $packData = $languagePack?->item_value;
        if ($packData) {
            $packData = ArrUtility::editValue($packData, 'name', 'editorUploadBtn', 'editorUploadButton');
            $packData = ArrUtility::editValue($packData, 'name', 'editorAllowProportionName', 'editorAllowPercentageName');
            $packData = ArrUtility::editValue($packData, 'name', 'editorAllowBtnName', 'editorAllowButtonName');
            $packData = ArrUtility::editValue($packData, 'name', 'editorCommentBtnTitle', 'editorCommentButtonTitle');
            $packData = ArrUtility::editValue($packData, 'name', 'editorCommentBtnName', 'editorCommentButtonName');

            $languagePack->item_value = $packData;
            $languagePack->save();
        }

        $langPackContents = Language::where('table_name', 'configs')->where('table_column', 'item_value')->where('table_key', 'language_pack_contents')->get();
        foreach ($langPackContents as $packContent) {
            $content = $packContent->lang_content;
            if (empty($content)) {
                continue;
            }

            $replaced = Str::replace('editorUploadBtn', 'editorUploadButton', $content);
            $replaced = Str::replace('editorAllowProportionName', 'editorAllowPercentageName', $replaced);
            $replaced = Str::replace('editorAllowBtnName', 'editorAllowButtonName', $replaced);
            $replaced = Str::replace('editorCommentBtnTitle', 'editorCommentButtonTitle', $replaced);
            $replaced = Str::replace('editorCommentBtnName', 'editorCommentButtonName', $replaced);

            $packContent->update([
                'lang_content' => $replaced,
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
