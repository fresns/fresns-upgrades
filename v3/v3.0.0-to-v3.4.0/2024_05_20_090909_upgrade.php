<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\Config;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v3.2.1
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 3.2.1"]);

        $configs = Config::whereIn('item_key', [
            'image_secret_app',
            'video_secret_app',
            'audio_secret_app',
            'document_secret_app',
            'image_bucket_domain',
            'video_bucket_domain',
            'audio_bucket_domain',
            'document_bucket_domain',
            'image_url_status',
            'image_url_key',
            'image_url_expire',
            'video_url_status',
            'video_url_key',
            'video_url_expire',
            'audio_url_status',
            'audio_url_key',
            'audio_url_expire',
            'document_url_status',
            'document_url_key',
            'document_url_expire',
        ])->get();

        foreach ($configs as $config) {
            $itemKey = $config->item_key;

            $newItemKey = match ($itemKey) {
                'image_secret_app' => 'image_access_domain',
                'video_secret_app' => 'video_access_domain',
                'audio_secret_app' => 'audio_access_domain',
                'document_secret_app' => 'document_access_domain',
                'image_bucket_domain' => 'image_bucket_endpoint',
                'video_bucket_domain' => 'video_bucket_endpoint',
                'audio_bucket_domain' => 'audio_bucket_endpoint',
                'document_bucket_domain' => 'document_bucket_endpoint',
                'image_url_status' => 'image_temporary_url_status',
                'image_url_key' => 'image_temporary_url_key',
                'image_url_expire' => 'image_temporary_url_expiration',
                'video_url_status' => 'video_temporary_url_status',
                'video_url_key' => 'video_temporary_url_key',
                'video_url_expire' => 'video_temporary_url_expiration',
                'audio_url_status' => 'audio_temporary_url_status',
                'audio_url_key' => 'audio_temporary_url_key',
                'audio_url_expire' => 'audio_temporary_url_expiration',
                'document_url_status' => 'document_temporary_url_status',
                'document_url_key' => 'document_temporary_url_key',
                'document_url_expire' => 'document_temporary_url_expiration',
                default => $itemKey,
            };

            $config->update([
                'item_key' => $newItemKey,
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
