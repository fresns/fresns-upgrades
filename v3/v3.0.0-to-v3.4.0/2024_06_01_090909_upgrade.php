<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\CodeMessage;
use App\Models\Config;
use App\Models\LanguagePack;
use App\Utilities\AppUtility;
use App\Utilities\ConfigUtility;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v3.3.0
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 3.3.0"]);

        $langs = LanguagePack::whereIn('lang_key', [
            'walletLogs',
            'walletLogType',
            'walletLogAmountTotal',
            'walletLogAmount',
            'walletLogSystemFee',
            'walletLogOpeningBalance',
            'walletLogClosingBalance',
            'walletLogTime',
            'walletLogRemark',
            'walletLogUser',
            'walletLogCode',
            'walletLogState',
            'walletLogType1',
            'walletLogType2',
            'walletLogType3',
            'walletLogType4',
            'walletLogType5',
            'walletLogType6',
            'walletLogType7',
            'walletLogType8',
            'walletLogState1',
            'walletLogState2',
            'walletLogState3',
            'walletLogState4',
            'walletLogState5',
            'userExtcreditsLogs',
            'userExtcreditsLogName',
            'userExtcreditsLogType',
            'userExtcreditsLogAmount',
            'userExtcreditsLogOpeningAmount',
            'userExtcreditsLogClosingAmount',
            'userExtcreditsLogApp',
            'userExtcreditsLogRemark',
            'userExtcreditsLogTime',
        ])->get();

        foreach ($langs as $lang) {
            $langKey = $lang->lang_key;

            $newLangKey = match ($langKey) {
                'walletLogs' => 'walletRecords',
                'walletLogType' => 'walletRecordType',
                'walletLogAmountTotal' => 'walletRecordAmountTotal',
                'walletLogAmount' => 'walletRecordAmount',
                'walletLogSystemFee' => 'walletRecordSystemFee',
                'walletLogOpeningBalance' => 'walletRecordOpeningBalance',
                'walletLogClosingBalance' => 'walletRecordClosingBalance',
                'walletLogTime' => 'walletRecordTime',
                'walletLogRemark' => 'walletRecordRemark',
                'walletLogUser' => 'walletRecordUser',
                'walletLogCode' => 'walletRecordCode',
                'walletLogState' => 'walletRecordState',
                'walletLogType1' => 'walletRecordType1',
                'walletLogType2' => 'walletRecordType2',
                'walletLogType3' => 'walletRecordType3',
                'walletLogType4' => 'walletRecordType4',
                'walletLogType5' => 'walletRecordType5',
                'walletLogType6' => 'walletRecordType6',
                'walletLogType7' => 'walletRecordType7',
                'walletLogType8' => 'walletRecordType8',
                'walletLogState1' => 'walletRecordState1',
                'walletLogState2' => 'walletRecordState2',
                'walletLogState3' => 'walletRecordState3',
                'walletLogState4' => 'walletRecordState4',
                'walletLogState5' => 'walletRecordState5',
                'userExtcreditsLogs' => 'userExtcreditsRecords',
                'userExtcreditsLogName' => 'userExtcreditsRecordName',
                'userExtcreditsLogType' => 'userExtcreditsRecordType',
                'userExtcreditsLogAmount' => 'userExtcreditsRecordAmount',
                'userExtcreditsLogOpeningAmount' => 'userExtcreditsRecordOpeningAmount',
                'userExtcreditsLogClosingAmount' => 'userExtcreditsRecordClosingAmount',
                'userExtcreditsLogApp' => 'userExtcreditsRecordApp',
                'userExtcreditsLogRemark' => 'userExtcreditsRecordRemark',
                'userExtcreditsLogTime' => 'userExtcreditsRecordTime',
                default => $langKey,
            };

            $lang->update([
                'lang_key' => $newLangKey,
            ]);
        }

        CodeMessage::updateOrCreate([
            'code' => '38101',
        ], [
            'app_fskey' => 'Fresns',
            'messages' => [
                'en' => 'You can only edit your own content',
                'zh-Hans' => '只能编辑自己的内容',
                'zh-Hant' => '只能編輯自己的內容',
            ],
        ]);

        CodeMessage::updateOrCreate([
            'code' => '38102',
        ], [
            'app_fskey' => 'Fresns',
            'messages' => [
                'en' => 'The content is being reviewed and can not be edited',
                'zh-Hans' => '内容审核中不可编辑',
                'zh-Hant' => '內容審核中不可編輯',
            ],
        ]);

        CodeMessage::updateOrCreate([
            'code' => '38103',
        ], [
            'app_fskey' => 'Fresns',
            'messages' => [
                'en' => 'The content has been published and can not be edited',
                'zh-Hans' => '内容已正式发表不可编辑',
                'zh-Hant' => '內容已正式發表不可編輯',
            ],
        ]);

        CodeMessage::updateOrCreate([
            'code' => '38104',
        ], [
            'app_fskey' => 'Fresns',
            'messages' => [
                'en' => 'Content being reviewed can not be submitted again',
                'zh-Hans' => '处于审核状态的内容不可再提交',
                'zh-Hant' => '處於審核狀態的內容不可再提交',
            ],
        ]);

        CodeMessage::updateOrCreate([
            'code' => '38105',
        ], [
            'app_fskey' => 'Fresns',
            'messages' => [
                'en' => 'Content being published can not be submitted again',
                'zh-Hans' => '处于发布状态的内容不可再提交',
                'zh-Hant' => '處於發布狀態的內容不可再提交',
            ],
        ]);

        CodeMessage::updateOrCreate([
            'code' => '38106',
        ], [
            'app_fskey' => 'Fresns',
            'messages' => [
                'en' => 'Failed to create draft comment. Only first-level comment can create draft',
                'zh-Hans' => '评论草稿创建失败，只有一级评论才能创建草稿',
                'zh-Hant' => '留言草稿創建失敗，只有一級留言才能創建草稿',
            ],
        ]);

        CodeMessage::updateOrCreate([
            'code' => '38107',
        ], [
            'app_fskey' => 'Fresns',
            'messages' => [
                'en' => 'Draft creation failed, draft box is full, please organize and create again',
                'zh-Hans' => '草稿创建失败，草稿箱已满，请整理后再创建',
                'zh-Hant' => '草稿創建失敗，草稿箱已滿，請整理後再創建',
            ],
        ]);

        CodeMessage::updateOrCreate([
            'code' => '38108',
        ], [
            'app_fskey' => 'Fresns',
            'messages' => [
                'en' => 'Comment failed, belongs to the post or has been deleted',
                'zh-Hans' => '评论失败，所属帖子或已删除',
                'zh-Hant' => '留言失敗，所屬帖子或已刪除',
            ],
        ]);

        CodeMessage::updateOrCreate([
            'code' => '38109',
        ], [
            'app_fskey' => 'Fresns',
            'messages' => [
                'en' => 'Comment failed, the post belongs to the comment function has been closed',
                'zh-Hans' => '评论失败，所属帖子已关闭评论功能',
                'zh-Hant' => '留言失敗，所屬貼文已關閉留言功能',
            ],
        ]);

        CodeMessage::updateOrCreate([
            'code' => '38110',
        ], [
            'app_fskey' => 'Fresns',
            'messages' => [
                'en' => 'The author of the post has made the comment private, do not support separate reconfiguration of comments',
                'zh-Hans' => '帖子作者已经设置评论私有，不支持单独再配置评论',
                'zh-Hant' => '貼文作者已經設定留言私有，不支援單獨再配置留言',
            ],
        ]);

        ConfigUtility::addFresnsConfigItems([
            [
                'item_key' => 'channel_timeline_hashtag_posts_name',
                'item_value' => '{"en":"Following Hashtags Posts","zh-Hans":"关注话题的帖子","zh-Hant":"跟隨話題的貼文"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_timeline_geotag_posts_name',
                'item_value' => '{"en":"Following Geotags Posts","zh-Hans":"关注地理的帖子","zh-Hant":"跟隨地理的貼文"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_timeline_hashtag_comments_name',
                'item_value' => '{"en":"Following Hashtags Comments","zh-Hans":"关注话题的评论","zh-Hant":"跟隨話題的留言"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 1,
            ],
            [
                'item_key' => 'channel_timeline_geotag_comments_name',
                'item_value' => '{"en":"Following Geotags Comments","zh-Hans":"关注地理的评论","zh-Hant":"跟隨地理的留言"}',
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_api' => 1,
            ],
        ]);

        $configs = Config::whereIn('item_key', [
            'channel_timeline_hashtag_posts_name',
            'channel_timeline_geotag_posts_name',
            'channel_timeline_hashtag_comments_name',
            'channel_timeline_geotag_comments_name',
        ])->get();

        foreach ($configs as $config) {
            $config->update([
                'is_custom' => false,
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
