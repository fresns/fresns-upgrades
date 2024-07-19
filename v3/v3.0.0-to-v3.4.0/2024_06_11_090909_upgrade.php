<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\Config;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v3.4.0
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 3.4.0"]);

        $configs = Config::whereIn('item_key', [
            'conversation_file_upload_type',
            'post_editor_image_upload_type',
            'post_editor_video_upload_type',
            'post_editor_audio_upload_type',
            'post_editor_document_upload_type',
            'comment_editor_image_upload_type',
            'comment_editor_video_upload_type',
            'comment_editor_audio_upload_type',
            'comment_editor_document_upload_type',
        ])->get();

        foreach ($configs as $config) {
            $itemKey = $config->item_key;

            $newItemKey = match ($itemKey) {
                'conversation_file_upload_type' => 'conversation_file_upload_method',
                'post_editor_image_upload_type' => 'post_editor_image_upload_method',
                'post_editor_video_upload_type' => 'post_editor_video_upload_method',
                'post_editor_audio_upload_type' => 'post_editor_audio_upload_method',
                'post_editor_document_upload_type' => 'post_editor_document_upload_method',
                'comment_editor_image_upload_type' => 'comment_editor_image_upload_method',
                'comment_editor_video_upload_type' => 'comment_editor_video_upload_method',
                'comment_editor_audio_upload_type' => 'comment_editor_audio_upload_method',
                'comment_editor_document_upload_type' => 'comment_editor_document_upload_method',
                default => $itemKey,
            };

            $config->update([
                'item_key' => $newItemKey,
            ]);
        }

        if (Schema::hasColumn('groups', 'follow_type')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->renameColumn('follow_type', 'follow_method');
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
