<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Helpers\CacheHelper;
use App\Models\Config;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;

class UpgradeTo20 extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to 20 (fresns v2.7.1)
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["2.7.1 => {$currentVersion}"]);

        if (version_compare('2.7.1', $currentVersion) == -1) {
            return;
        }

        $siteRegisterEmail = Config::where('item_key', 'site_register_email')->first();
        if ($siteRegisterEmail) {
            $siteRegisterEmail->update([
                'item_key' => 'site_email_register',
            ]);
        }

        $siteRegisterPhone = Config::where('item_key', 'site_register_phone')->first();
        if ($siteRegisterPhone) {
            $siteRegisterPhone->update([
                'item_key' => 'site_phone_register',
            ]);
        }

        $siteEmailLogin = Config::where('item_key', 'site_email_login')->first();
        if (! $siteEmailLogin) {
            $newConfig = new Config;
            $newConfig->item_key = 'site_email_login';
            $newConfig->item_value = 'true';
            $newConfig->item_type = 'boolean';
            $newConfig->item_tag = 'general';
            $newConfig->is_multilingual = 0;
            $newConfig->is_custom = 0;
            $newConfig->is_api = 1;
            $newConfig->save();
        }

        $sitePhoneLogin = Config::where('item_key', 'site_phone_login')->first();
        if (! $sitePhoneLogin) {
            $newConfig = new Config;
            $newConfig->item_key = 'site_phone_login';
            $newConfig->item_value = 'false';
            $newConfig->item_type = 'boolean';
            $newConfig->item_tag = 'general';
            $newConfig->is_multilingual = 0;
            $newConfig->is_custom = 0;
            $newConfig->is_api = 1;
            $newConfig->save();
        }

        $imageBucketArea = Config::where('item_key', 'image_bucket_area')->first();
        if ($imageBucketArea) {
            $imageBucketArea->update([
                'item_key' => 'image_bucket_region',
            ]);
        }

        $videoBucketArea = Config::where('item_key', 'video_bucket_area')->first();
        if ($videoBucketArea) {
            $videoBucketArea->update([
                'item_key' => 'video_bucket_region',
            ]);
        }

        $audioBucketArea = Config::where('item_key', 'audio_bucket_area')->first();
        if ($audioBucketArea) {
            $audioBucketArea->update([
                'item_key' => 'audio_bucket_region',
            ]);
        }

        $documentBucketArea = Config::where('item_key', 'document_bucket_area')->first();
        if ($documentBucketArea) {
            $documentBucketArea->update([
                'item_key' => 'document_bucket_region',
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
