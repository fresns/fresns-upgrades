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
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpgradeTo17 extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to 17 (fresns v2.6.0)
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["2.6.0 => {$currentVersion}"]);

        if (version_compare('2.6.0', $currentVersion) == -1) {
            return;
        }

        if (Schema::hasColumn('files', 'video_cover_path')) {
            Schema::table('files', function (Blueprint $table) {
                $table->renameColumn('video_cover_path', 'video_poster_path');
            });
        }

        if (Schema::hasColumn('files', 'video_gif_path')) {
            Schema::table('files', function (Blueprint $table) {
                $table->dropColumn('video_gif_path');
            });
        }

        if (Schema::hasColumn('files', 'video_poster_path')) {
            DB::table('files')->update([
                'video_poster_path' => null,
            ]);
        }

        // panel_auth
        $panelAuthIp = Config::where('item_key', 'panel_auth_ip')->first();
        if ($panelAuthIp) {
            $panelAuthIp->item_key = 'panel_auth';
            $panelAuthIp->item_value = json_decode('{"email":true,"phone":true,"aid":true,"ipv4":[],"ipv6":[]}', true);
            $panelAuthIp->item_type = 'object';
            $panelAuthIp->item_tag = 'systems';
            $panelAuthIp->is_multilingual = 0;
            $panelAuthIp->is_custom = 0;
            $panelAuthIp->is_api = 0;
            $panelAuthIp->save();
        }

        // developer_mode
        $panelLoginByAid = Config::where('item_key', 'panel_login_by_aid')->first();
        if ($panelLoginByAid) {
            $panelLoginByAid->item_key = 'developer_mode';
            $panelLoginByAid->item_value = json_decode('{"cache":true,"apiSignature":true}', true);
            $panelLoginByAid->item_type = 'object';
            $panelLoginByAid->item_tag = 'systems';
            $panelLoginByAid->is_multilingual = 0;
            $panelLoginByAid->is_custom = 0;
            $panelLoginByAid->is_api = 0;
            $panelLoginByAid->save();
        }

        // video_transcode_handle_position
        $videoTranscodeHandlePosition = Config::where('item_key', 'video_transcode_handle_position')->first();
        if (! $videoTranscodeHandlePosition) {
            $videoWatermark = Config::where('item_key', 'video_watermark')->first();

            if ($videoWatermark) {
                $videoWatermark->item_key = 'video_transcode_handle_position';
                $videoWatermark->item_value = null;
                $videoWatermark->item_type = 'string';
                $videoWatermark->item_tag = 'storageVideos';
                $videoWatermark->is_multilingual = 0;
                $videoWatermark->is_custom = 0;
                $videoWatermark->is_api = 0;
                $videoWatermark->save();
            } else {
                $newConfig = new Config;
                $newConfig->item_key = 'video_transcode_handle_position';
                $newConfig->item_value = null;
                $newConfig->item_type = 'string';
                $newConfig->item_tag = 'storageVideos';
                $newConfig->is_multilingual = 0;
                $newConfig->is_custom = 0;
                $newConfig->is_api = 0;
                $newConfig->save();
            }
        }

        // video_poster_parameter
        $videoPoster = Config::where('item_key', 'video_poster_parameter')->first();
        if (! $videoPoster) {
            $videoScreenshot = Config::where('item_key', 'video_screenshot')->first();

            if ($videoScreenshot) {
                $videoScreenshot->item_key = 'video_poster_parameter';
                $videoScreenshot->item_value = $videoScreenshot->item_value;
                $videoScreenshot->item_type = 'string';
                $videoScreenshot->item_tag = 'storageVideos';
                $videoScreenshot->is_multilingual = 0;
                $videoScreenshot->is_custom = 0;
                $videoScreenshot->is_api = 0;
                $videoScreenshot->save();
            } else {
                $newConfig = new Config;
                $newConfig->item_key = 'video_poster_parameter';
                $newConfig->item_value = null;
                $newConfig->item_type = 'string';
                $newConfig->item_tag = 'storageVideos';
                $newConfig->is_multilingual = 0;
                $newConfig->is_custom = 0;
                $newConfig->is_api = 0;
                $newConfig->save();
            }
        }

        // video_poster_handle_position
        $videoPosterHandlePosition = Config::where('item_key', 'video_poster_handle_position')->first();
        if (! $videoPosterHandlePosition) {
            $videoGift = Config::where('item_key', 'video_gift')->first();

            if ($videoGift) {
                $videoGift->item_key = 'video_poster_handle_position';
                $videoGift->item_value = null;
                $videoGift->item_type = 'string';
                $videoGift->item_tag = 'storageVideos';
                $videoGift->is_multilingual = 0;
                $videoGift->is_custom = 0;
                $videoGift->is_api = 0;
                $videoGift->save();
            } else {
                $newConfig = new Config;
                $newConfig->item_key = 'video_poster_handle_position';
                $newConfig->item_value = null;
                $newConfig->item_type = 'string';
                $newConfig->item_tag = 'storageVideos';
                $newConfig->is_multilingual = 0;
                $newConfig->is_custom = 0;
                $newConfig->is_api = 0;
                $newConfig->save();
            }
        }

        // audio_transcode_handle_position
        $audioTranscodeHandlePosition = Config::where('item_key', 'audio_transcode_handle_position')->first();
        if (! $audioTranscodeHandlePosition) {
            $imageThumbAvatar = Config::where('item_key', 'image_thumb_avatar')->first();

            if ($imageThumbAvatar) {
                $imageThumbAvatar->item_key = 'audio_transcode_handle_position';
                $imageThumbAvatar->item_value = null;
                $imageThumbAvatar->item_type = 'string';
                $imageThumbAvatar->item_tag = 'storageAudios';
                $imageThumbAvatar->is_multilingual = 0;
                $imageThumbAvatar->is_custom = 0;
                $imageThumbAvatar->is_api = 0;
                $imageThumbAvatar->save();
            } else {
                $newConfig = new Config;
                $newConfig->item_key = 'audio_transcode_handle_position';
                $newConfig->item_value = null;
                $newConfig->item_type = 'string';
                $newConfig->item_tag = 'storageAudios';
                $newConfig->is_multilingual = 0;
                $newConfig->is_custom = 0;
                $newConfig->is_api = 0;
                $newConfig->save();
            }
        }

        // video_transcode
        $videoTranscode = Config::where('item_key', 'video_transcode')->first();
        $videoTranscode?->update([
            'item_key' => 'video_transcode_parameter',
        ]);

        // audio_transcode
        $audioTranscode = Config::where('item_key', 'audio_transcode')->first();
        $audioTranscode?->update([
            'item_key' => 'audio_transcode_parameter',
        ]);

        // mention_status
        $mentionStatus = Config::where('item_key', 'mention_status')->first();
        if (! $mentionStatus) {
            $panelLoginByEmail = Config::where('item_key', 'panel_login_by_email')->first();

            if ($panelLoginByEmail) {
                $panelLoginByEmail->item_key = 'mention_status';
                $panelLoginByEmail->item_value = 'true';
                $panelLoginByEmail->item_type = 'boolean';
                $panelLoginByEmail->item_tag = 'interactions';
                $panelLoginByEmail->is_multilingual = 0;
                $panelLoginByEmail->is_custom = 0;
                $panelLoginByEmail->is_api = 1;
                $panelLoginByEmail->save();
            } else {
                $newConfig = new Config;
                $newConfig->item_key = 'mention_status';
                $newConfig->item_value = 'true';
                $newConfig->item_type = 'boolean';
                $newConfig->item_tag = 'interactions';
                $newConfig->is_multilingual = 0;
                $newConfig->is_custom = 0;
                $newConfig->is_api = 1;
                $newConfig->save();
            }
        }

        // hashtag_status
        $hashtagStatus = Config::where('item_key', 'hashtag_status')->first();
        if (! $hashtagStatus) {
            $panelLoginByPhone = Config::where('item_key', 'panel_login_by_phone')->first();

            if ($panelLoginByPhone) {
                $panelLoginByPhone->item_key = 'hashtag_status';
                $panelLoginByPhone->item_value = 'true';
                $panelLoginByPhone->item_type = 'boolean';
                $panelLoginByPhone->item_tag = 'interactions';
                $panelLoginByPhone->is_multilingual = 0;
                $panelLoginByPhone->is_custom = 0;
                $panelLoginByPhone->is_api = 1;
                $panelLoginByPhone->save();
            } else {
                $newConfig = new Config;
                $newConfig->item_key = 'hashtag_status';
                $newConfig->item_value = 'true';
                $newConfig->item_type = 'boolean';
                $newConfig->item_tag = 'interactions';
                $newConfig->is_multilingual = 0;
                $newConfig->is_custom = 0;
                $newConfig->is_api = 1;
                $newConfig->save();
            }
        }

        // hashtag_format
        $hashtagFormat = Config::where('item_key', 'hashtag_show')->first();
        $hashtagFormat?->update([
            'item_key' => 'hashtag_format',
        ]);

        // send_email_service
        $sendEmailService = Config::where('item_key', 'send_email_service')->first();
        $sendEmailService?->update([
            'is_api' => 1,
        ]);

        // send_sms_service
        $sendSmsService = Config::where('item_key', 'send_sms_service')->first();
        $sendSmsService?->update([
            'is_api' => 1,
        ]);

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
