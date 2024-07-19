<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\CodeMessage;
use App\Models\Config;
use App\Models\LanguagePack;
use App\Models\Role;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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

        // file
        if (! Schema::hasColumn('files', 'is_uploaded')) {
            Schema::table('files', function (Blueprint $table) {
                $table->boolean('is_uploaded')->default(1)->index('file_is_uploaded')->after('warning_type');
            });
        }

        if (Schema::hasColumn('files', 'more_info')) {
            Schema::table('files', function (Blueprint $table) {
                $table->dropColumn('more_info');
            });
        }

        if (Schema::hasColumn('files', 'md5')) {
            Schema::table('files', function (Blueprint $table) {
                $table->dropColumn('md5');
            });
        }

        if (Schema::hasColumn('files', 'audio_time')) {
            Schema::table('files', function (Blueprint $table) {
                $table->dropColumn('audio_time');
            });
        }

        if (Schema::hasColumn('files', 'image_width')) {
            Schema::table('files', function (Blueprint $table) {
                $table->renameColumn('image_width', 'width');
            });
        }

        if (Schema::hasColumn('files', 'image_height')) {
            Schema::table('files', function (Blueprint $table) {
                $table->renameColumn('image_height', 'height');
            });
        }

        if (Schema::hasColumn('files', 'image_is_long')) {
            Schema::table('files', function (Blueprint $table) {
                $table->renameColumn('image_is_long', 'is_long_image');
            });
        }

        if (Schema::hasColumn('files', 'video_time')) {
            Schema::table('files', function (Blueprint $table) {
                $table->renameColumn('video_time', 'duration');
            });
        }

        try {
            Schema::table('files', function (Blueprint $table) {
                $table->index('is_enabled', 'file_is_enabled');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('files', function (Blueprint $table) {
                $table->index(['sha', 'sha_type'], 'file_sha');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('files', function (Blueprint $table) {
                $table->index('physical_deletion', 'file_physical_deletion');
            });
        } catch (\Exception $e) {}

        // file_usages
        if (! Schema::hasColumn('file_usages', 'more_info')) {
            Schema::table('file_usages', function (Blueprint $table) {
                switch (config('database.default')) {
                    case 'pgsql':
                        $table->jsonb('more_info')->nullable()->after('sort_order');
                        break;
    
                    default:
                        $table->json('more_info')->nullable()->after('sort_order');
                }
            });
        }

        // error code
        $errorCode = CodeMessage::where('code', 30008)->first();
        if (! $errorCode) {
            $codeInput = [
                'app_fskey' => 'Fresns',
                'code' => 30008,
                'messages' => json_decode('{"en":"Operation failed, please try again","zh-Hans":"操作失败，请重试","zh-Hant":"操作失敗，請重試"}', true),
            ];

            CodeMessage::create($codeInput);
        }

        // connects
        $connects = Config::where('item_key', 'connects')->first();
        if ($connects) {
            $connects->item_key = 'connects';
            $connects->item_value = json_decode('[{"id":1,"name":"Other"},{"id":2,"name":"Fresns"},{"id":3,"name":"SSO"},{"id":4,"name":"GitHub"},{"id":5,"name":"GitLab"},{"id":6,"name":"Bitbucket"},{"id":7,"name":"Google"},{"id":8,"name":"Facebook"},{"id":9,"name":"Instagram"},{"id":10,"name":"Twitter"},{"id":11,"name":"Discord"},{"id":12,"name":"Telegram"},{"id":13,"name":"Apple"},{"id":14,"name":"Microsoft"},{"id":15,"name":"LinkedIn"},{"id":16,"name":"PayPal"},{"id":17,"name":"Slack"},{"id":18,"name":"Netlify"},{"id":19,"name":"LINE"},{"id":20,"name":"KakaoTalk"},{"id":21,"name":"Lark"},{"id":22,"name":"Steam"},{"id":23,"name":"WeChat Open Platform (UnionID)"},{"id":24,"name":"WeChat Official Accounts Platform"},{"id":25,"name":"WeChat Mini Program"},{"id":26,"name":"WeChat Mobile Application"},{"id":27,"name":"WeChat Website Application"},{"id":28,"name":"WeCom"},{"id":29,"name":"Tencent QQ Open Platform (UnionID)"},{"id":30,"name":"Tencent QQ Mini Program"},{"id":31,"name":"Tencent QQ Mobile Application"},{"id":32,"name":"Tencent QQ Website Application"},{"id":33,"name":"Gitee"},{"id":34,"name":"Weibo"},{"id":35,"name":"Alipay"},{"id":36,"name":"ByteDance"}]', true);
            $connects->save();
        }

        // platforms
        $platforms = Config::where('item_key', 'platforms')->first();
        if ($platforms) {
            $platforms->item_key = 'platforms';
            $platforms->item_value = json_decode('[{"id":1,"name":"Other"},{"id":2,"name":"Desktop Web"},{"id":3,"name":"Mobile Web"},{"id":4,"name":"Responsive Web"},{"id":5,"name":"iOS App"},{"id":6,"name":"Android App"},{"id":7,"name":"Mac App"},{"id":8,"name":"Windows App"},{"id":9,"name":"Linux App"},{"id":10,"name":"Vision App"},{"id":11,"name":"Mini Program"}]', true);
            $platforms->save();
        }

        // editorUploadTipMaxTime
        $editorUploadTipMaxTime = LanguagePack::where('lang_key', 'editorUploadTipMaxTime')->first();
        if ($editorUploadTipMaxTime) {
            $editorUploadTipMaxTime->lang_key = 'editorUploadTipMaxDuration';
            $editorUploadTipMaxTime->save();
        }

        // editorUploadTipNumber
        $editorUploadTipNumber = LanguagePack::where('lang_key', 'editorUploadTipNumber')->first();
        if ($editorUploadTipNumber) {
            $editorUploadTipNumber->lang_key = 'editorUploadTipMaxNumber';
            $editorUploadTipNumber->save();
        }

        // configs
        $configs = Config::whereIn('item_key', [
            'video_max_time',
            'audio_max_time',
            'document_online_preview',
            'post_editor_image_upload_number',
            'post_editor_video_upload_number',
            'post_editor_audio_upload_number',
            'post_editor_document_upload_number',
            'comment_editor_image_upload_number',
            'comment_editor_video_upload_number',
            'comment_editor_audio_upload_number',
            'comment_editor_document_upload_number',
        ])->get();

        foreach ($configs as $config) {
            $itemKey = $config->item_key;

            $newItemKey = match ($itemKey) {
                'video_max_time' => 'video_max_duration',
                'audio_max_time' => 'audio_max_duration',
                'document_online_preview' => 'document_preview_service',
                'post_editor_image_upload_number' => 'post_editor_image_max_upload_number',
                'post_editor_video_upload_number' => 'post_editor_video_max_upload_number',
                'post_editor_audio_upload_number' => 'post_editor_audio_max_upload_number',
                'post_editor_document_upload_number' => 'post_editor_document_max_upload_number',
                'comment_editor_image_upload_number' => 'comment_editor_image_max_upload_number',
                'comment_editor_video_upload_number' => 'comment_editor_video_max_upload_number',
                'comment_editor_audio_upload_number' => 'comment_editor_audio_max_upload_number',
                'comment_editor_document_upload_number' => 'comment_editor_document_max_upload_number',
                default => $itemKey,
            };

            if ($newItemKey == 'document_preview_service') {
                $config->update([
                    'item_key' => $newItemKey,
                    'item_value' => null,
                    'item_type' => 'plugin',
                ]);
            } else {
                $config->update([
                    'item_key' => $newItemKey,
                ]);
            }
        }

        // roles
        $roles = Role::all();
        foreach ($roles as $role) {
            $permissions = $role->permissions;

            $permissionsStr = json_encode($permissions);

            $newPermissions = Str::replace('video_max_time', 'video_max_duration', $permissionsStr);
            $newPermissions = Str::replace('audio_max_time', 'audio_max_duration', $newPermissions);
            $newPermissions = Str::replace('post_editor_image_upload_number', 'post_editor_image_max_upload_number', $newPermissions);
            $newPermissions = Str::replace('post_editor_video_upload_number', 'post_editor_video_max_upload_number', $newPermissions);
            $newPermissions = Str::replace('post_editor_audio_upload_number', 'post_editor_audio_max_upload_number', $newPermissions);
            $newPermissions = Str::replace('post_editor_document_upload_number', 'post_editor_document_max_upload_number', $newPermissions);
            $newPermissions = Str::replace('comment_editor_image_upload_number', 'comment_editor_image_max_upload_number', $newPermissions);
            $newPermissions = Str::replace('comment_editor_video_upload_number', 'comment_editor_video_max_upload_number', $newPermissions);
            $newPermissions = Str::replace('comment_editor_audio_upload_number', 'comment_editor_audio_max_upload_number', $newPermissions);
            $newPermissions = Str::replace('comment_editor_document_upload_number', 'comment_editor_document_max_upload_number', $newPermissions);

            $permissionsArr = json_decode($newPermissions, true);

            $role->update([
                'permissions' => $permissionsArr,
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
