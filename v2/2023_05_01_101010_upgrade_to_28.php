<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Helpers\CacheHelper;
use App\Models\CodeMessage;
use App\Models\Config;
use App\Models\Language;
use App\Utilities\AppUtility;
use App\Utilities\ArrUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UpgradeTo28 extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v2.11.0
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 2.11.0"]);

        if (version_compare('2.11.0', $currentVersion) == -1) {
            return;
        }

        // fskey
        $crontabItems = Config::where('item_key', 'crontab_items')->first();
        $crontabArr = $crontabItems?->item_value;
        if ($crontabArr) {
            $crontabAsString = json_encode($crontabArr);
            $crontabItemsReplaced = Str::replace('unikey', 'fskey', $crontabAsString);
            $crontabItems?->update([
                'item_value' => $crontabItemsReplaced,
            ]);
        }

        $subscribeItems = Config::where('item_key', 'subscribe_items')->first();
        $subscribeArr = $subscribeItems?->item_value;
        if ($subscribeArr) {
            $subscribeAsString = json_encode($subscribeArr);
            $subscribeItemsReplaced = Str::replace('unikey', 'fskey', $subscribeAsString);
            $subscribeItems?->update([
                'item_value' => $subscribeItemsReplaced,
            ]);
        }

        $extcredits1_state = Config::where('item_key', 'extcredits1_status')->first();
        $extcredits1_state?->update([
            'item_key' => 'extcredits1_state',
        ]);

        $extcredits2_state = Config::where('item_key', 'extcredits2_status')->first();
        $extcredits2_state?->update([
            'item_key' => 'extcredits2_state',
        ]);

        $extcredits3_state = Config::where('item_key', 'extcredits3_status')->first();
        $extcredits3_state?->update([
            'item_key' => 'extcredits3_state',
        ]);

        $extcredits4_state = Config::where('item_key', 'extcredits4_status')->first();
        $extcredits4_state?->update([
            'item_key' => 'extcredits4_state',
        ]);

        $extcredits5_state = Config::where('item_key', 'extcredits5_status')->first();
        $extcredits5_state?->update([
            'item_key' => 'extcredits5_state',
        ]);

        if (! Schema::hasColumn('session_keys', 'is_read_only')) {
            Schema::table('session_keys', function (Blueprint $table) {
                $table->unsignedTinyInteger('is_read_only')->default(0)->after('app_secret');
            });
        }

        if (Schema::hasColumn('session_keys', 'plugin_unikey')) {
            Schema::table('session_keys', function (Blueprint $table) {
                $table->renameColumn('plugin_unikey', 'plugin_fskey');
            });
        }

        if (Schema::hasColumn('session_logs', 'plugin_unikey')) {
            Schema::table('session_logs', function (Blueprint $table) {
                $table->renameColumn('plugin_unikey', 'plugin_fskey');
            });
        }

        if (Schema::hasColumn('plugins', 'unikey')) {
            Schema::table('plugins', function (Blueprint $table) {
                $table->renameColumn('unikey', 'fskey');
            });
        }

        if (Schema::hasColumn('plugin_usages', 'plugin_unikey')) {
            Schema::table('plugin_usages', function (Blueprint $table) {
                $table->renameColumn('plugin_unikey', 'plugin_fskey');
            });
        }

        if (Schema::hasColumn('plugin_badges', 'plugin_unikey')) {
            Schema::table('plugin_badges', function (Blueprint $table) {
                $table->renameColumn('plugin_unikey', 'plugin_fskey');
            });
        }

        if (Schema::hasColumn('plugin_callbacks', 'plugin_unikey')) {
            Schema::table('plugin_callbacks', function (Blueprint $table) {
                $table->renameColumn('plugin_unikey', 'plugin_fskey');
                $table->renameColumn('use_plugin_unikey', 'use_plugin_fskey');
            });
        }

        if (Schema::hasColumn('accounts', 'verify_plugin_unikey')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->renameColumn('verify_plugin_unikey', 'verify_plugin_fskey');
            });
        }

        if (Schema::hasColumn('account_connects', 'plugin_unikey')) {
            Schema::table('account_connects', function (Blueprint $table) {
                $table->renameColumn('plugin_unikey', 'plugin_fskey');
            });
        }

        if (Schema::hasColumn('account_wallet_logs', 'plugin_unikey')) {
            Schema::table('account_wallet_logs', function (Blueprint $table) {
                $table->renameColumn('plugin_unikey', 'plugin_fskey');
            });
        }

        if (Schema::hasColumn('archives', 'plugin_unikey')) {
            Schema::table('archives', function (Blueprint $table) {
                $table->renameColumn('plugin_unikey', 'plugin_fskey');
            });
        }

        if (Schema::hasColumn('archive_usages', 'plugin_unikey')) {
            Schema::table('archive_usages', function (Blueprint $table) {
                $table->renameColumn('plugin_unikey', 'plugin_fskey');
            });
        }

        if (Schema::hasColumn('code_messages', 'plugin_unikey')) {
            Schema::table('code_messages', function (Blueprint $table) {
                $table->renameColumn('plugin_unikey', 'plugin_fskey');
            });
        }

        if (Schema::hasColumn('comment_appends', 'editor_unikey')) {
            Schema::table('comment_appends', function (Blueprint $table) {
                $table->renameColumn('editor_unikey', 'editor_fskey');
            });
        }

        if (Schema::hasColumn('comment_logs', 'editor_unikey')) {
            Schema::table('comment_logs', function (Blueprint $table) {
                $table->renameColumn('editor_unikey', 'editor_fskey');
            });
        }

        if (Schema::hasColumn('extends', 'plugin_unikey')) {
            Schema::table('extends', function (Blueprint $table) {
                $table->renameColumn('plugin_unikey', 'plugin_fskey');
            });
        }

        if (Schema::hasColumn('extend_usages', 'plugin_unikey')) {
            Schema::table('extend_usages', function (Blueprint $table) {
                $table->renameColumn('plugin_unikey', 'plugin_fskey');
            });
        }

        if (Schema::hasColumn('file_downloads', 'plugin_unikey')) {
            Schema::table('file_downloads', function (Blueprint $table) {
                $table->renameColumn('plugin_unikey', 'plugin_fskey');
            });
        }

        if (Schema::hasColumn('groups', 'plugin_unikey')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->renameColumn('plugin_unikey', 'plugin_fskey');
            });
        }

        if (Schema::hasColumn('notifications', 'plugin_unikey')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->renameColumn('plugin_unikey', 'plugin_fskey');
            });
        }

        if (Schema::hasColumn('operations', 'plugin_unikey')) {
            Schema::table('operations', function (Blueprint $table) {
                $table->renameColumn('plugin_unikey', 'plugin_fskey');
            });
        }

        if (Schema::hasColumn('operation_usages', 'plugin_unikey')) {
            Schema::table('operation_usages', function (Blueprint $table) {
                $table->renameColumn('plugin_unikey', 'plugin_fskey');
            });
        }

        if (Schema::hasColumn('placements', 'plugin_unikey')) {
            Schema::table('placements', function (Blueprint $table) {
                $table->renameColumn('plugin_unikey', 'plugin_fskey');
            });
        }

        if (Schema::hasColumn('post_appends', 'editor_unikey')) {
            Schema::table('post_appends', function (Blueprint $table) {
                $table->renameColumn('editor_unikey', 'editor_fskey');
                $table->renameColumn('allow_plugin_unikey', 'allow_plugin_fskey');
                $table->renameColumn('user_list_plugin_unikey', 'user_list_plugin_fskey');
                $table->renameColumn('comment_btn_plugin_unikey', 'comment_btn_plugin_fskey');
            });
        }

        if (Schema::hasColumn('post_users', 'plugin_unikey')) {
            Schema::table('post_users', function (Blueprint $table) {
                $table->renameColumn('plugin_unikey', 'plugin_fskey');
            });
        }

        if (Schema::hasColumn('post_logs', 'editor_unikey')) {
            Schema::table('post_logs', function (Blueprint $table) {
                $table->renameColumn('editor_unikey', 'editor_fskey');
            });
        }

        // lang pack add key
        $languagePack = Config::where('item_key', 'language_pack')->first();
        if ($languagePack) {
            $packData = $languagePack->item_value;
            $packData = ArrUtility::editValue($packData, 'name', 'contentReview', 'contentReviewPending');

            $addPackKeys = [
                [
                    'name' => 'userExtcreditsLogs',
                    'canDelete' => false,
                ],
                [
                    'name' => 'userExtcreditsLogName',
                    'canDelete' => false,
                ],
                [
                    'name' => 'userExtcreditsLogType',
                    'canDelete' => false,
                ],
                [
                    'name' => 'userExtcreditsLogAmount',
                    'canDelete' => false,
                ],
                [
                    'name' => 'userExtcreditsLogOpeningAmount',
                    'canDelete' => false,
                ],
                [
                    'name' => 'userExtcreditsLogClosingAmount',
                    'canDelete' => false,
                ],
                [
                    'name' => 'userExtcreditsLogPlugin',
                    'canDelete' => false,
                ],
                [
                    'name' => 'userExtcreditsLogRemark',
                    'canDelete' => false,
                ],
                [
                    'name' => 'userExtcreditsLogTime',
                    'canDelete' => false,
                ],
                [
                    'name' => 'walletLogType',
                    'canDelete' => false,
                ],
                [
                    'name' => 'walletLogAmountTotal',
                    'canDelete' => false,
                ],
                [
                    'name' => 'walletLogAmount',
                    'canDelete' => false,
                ],
                [
                    'name' => 'walletLogSystemFee',
                    'canDelete' => false,
                ],
                [
                    'name' => 'walletLogOpeningBalance',
                    'canDelete' => false,
                ],
                [
                    'name' => 'walletLogClosingBalance',
                    'canDelete' => false,
                ],
                [
                    'name' => 'walletLogTime',
                    'canDelete' => false,
                ],
                [
                    'name' => 'walletLogRemark',
                    'canDelete' => false,
                ],
                [
                    'name' => 'walletLogUser',
                    'canDelete' => false,
                ],
                [
                    'name' => 'walletLogStatus',
                    'canDelete' => false,
                ],
                [
                    'name' => 'editorCommentDisable',
                    'canDelete' => false,
                ],
                [
                    'name' => 'editorCommentPrivate',
                    'canDelete' => false,
                ],
                [
                    'name' => 'contentReviewRejected',
                    'canDelete' => false,
                ],
            ];

            $newData = array_merge($packData, $addPackKeys);

            $languagePack->item_value = $newData;
            $languagePack->save();
        }

        $langPackContents = Language::where('table_name', 'configs')->where('table_column', 'item_value')->where('table_key', 'language_pack_contents')->get();
        foreach ($langPackContents as $packContent) {
            $content = $packContent->lang_content;
            if (empty($content)) {
                continue;
            }

            $replaced = Str::replace('contentReview', 'contentReviewPending', $content);
            $replaced = Str::replace('editorAllowTitle', 'editorReadConfigTitle', $replaced);
            $replaced = Str::replace('editorAllowRoleName', 'editorReadConfigRoleName', $replaced);
            $replaced = Str::replace('editorAllowUserName', 'editorReadConfigUserName', $replaced);
            $replaced = Str::replace('editorAllowPercentageName', 'editorReadConfigPercentageName', $replaced);
            $replaced = Str::replace('editorAllowBtnName', 'editorReadConfigBtnName', $replaced);
            $replaced = Str::replace('contentCreator', 'contentAuthor', $replaced);
            $replaced = Str::replace('contentAllowInfo', 'contentPreReadInfo', $replaced);
            $replaced = Str::replace('contentAuthorDeactivate', 'userDeactivate', $replaced);

            $newContent = (object) json_decode($replaced, true);

            $langAddContent = match ($packContent->lang_tag) {
                'en' => [
                    'userExtcreditsLogs' => 'Extcredits History',
                    'userExtcreditsLogName' => 'Extcredits',
                    'userExtcreditsLogType' => 'Type',
                    'userExtcreditsLogAmount' => 'Amount',
                    'userExtcreditsLogOpeningAmount' => 'Opening Amount',
                    'userExtcreditsLogClosingAmount' => 'Closing Amount',
                    'userExtcreditsLogPlugin' => 'Plugin',
                    'userExtcreditsLogRemark' => 'Remark',
                    'userExtcreditsLogTime' => 'Time',
                    'walletLogType' => 'Type',
                    'walletLogAmountTotal' => 'Amount Total',
                    'walletLogAmount' => 'Amount',
                    'walletLogSystemFee' => 'System Fee',
                    'walletLogOpeningBalance' => 'Opening Balance',
                    'walletLogClosingBalance' => 'Closing Balance',
                    'walletLogTime' => 'Time',
                    'walletLogRemark' => 'Remark',
                    'walletLogUser' => 'User',
                    'walletLogStatus' => 'Status',
                    'walletLogType1' => 'Recharge',
                    'walletLogType2' => 'Unfreeze',
                    'walletLogType3' => 'Transaction',
                    'walletLogType4' => 'Withdraw',
                    'walletLogType5' => 'Freeze',
                    'walletLogType6' => 'Transaction',
                    'editorCommentDisable' => 'Disable Comment',
                    'editorCommentPrivate' => 'Only visible to post author',
                    'contentReviewRejected' => 'Content review rejected',
                ],
                'zh-Hans' => [
                    'userExtcreditsLogs' => '用户扩展分值记录',
                    'userExtcreditsLogName' => '分值名称',
                    'userExtcreditsLogType' => '操作类型',
                    'userExtcreditsLogAmount' => '分值',
                    'userExtcreditsLogOpeningAmount' => '期初分值',
                    'userExtcreditsLogClosingAmount' => '期末分值',
                    'userExtcreditsLogPlugin' => '插件',
                    'userExtcreditsLogRemark' => '备注',
                    'userExtcreditsLogTime' => '时间',
                    'walletLogType' => '交易类型',
                    'walletLogAmountTotal' => '交易总额',
                    'walletLogAmount' => '交易金额',
                    'walletLogSystemFee' => '交易服务费',
                    'walletLogOpeningBalance' => '期初余额',
                    'walletLogClosingBalance' => '期末余额',
                    'walletLogTime' => '交易时间',
                    'walletLogRemark' => '交易备注',
                    'walletLogUser' => '交易用户',
                    'walletLogStatus' => '交易状态',
                    'walletLogType1' => '充值',
                    'walletLogType2' => '解冻',
                    'walletLogType3' => '交易',
                    'walletLogType4' => '提现',
                    'walletLogType5' => '冻结',
                    'walletLogType6' => '交易',
                    'editorCommentDisable' => '禁止评论',
                    'editorCommentPrivate' => '仅帖子作者可见',
                    'contentReviewRejected' => '内容审核拒绝',
                ],
                'zh-Hant' => [
                    'userExtcreditsLogs' => '用戶擴展分值記錄',
                    'userExtcreditsLogName' => '分值名稱',
                    'userExtcreditsLogType' => '操作類型',
                    'userExtcreditsLogAmount' => '分值',
                    'userExtcreditsLogOpeningAmount' => '期初分值',
                    'userExtcreditsLogClosingAmount' => '期末分值',
                    'userExtcreditsLogPlugin' => '外掛',
                    'userExtcreditsLogRemark' => '備註',
                    'userExtcreditsLogTime' => '時間',
                    'walletLogType' => '交易類型',
                    'walletLogAmountTotal' => '交易總額',
                    'walletLogAmount' => '交易金額',
                    'walletLogSystemFee' => '交易服務費',
                    'walletLogOpeningBalance' => '期初餘額',
                    'walletLogClosingBalance' => '期末餘額',
                    'walletLogTime' => '交易時間',
                    'walletLogRemark' => '交易備註',
                    'walletLogUser' => '交易用戶',
                    'walletLogStatus' => '交易狀態',
                    'walletLogType1' => '充值',
                    'walletLogType2' => '解凍',
                    'walletLogType3' => '交易',
                    'walletLogType4' => '提現',
                    'walletLogType5' => '凍結',
                    'walletLogType6' => '交易',
                    'editorCommentDisable' => '禁止留言',
                    'editorCommentPrivate' => '僅貼文作者可見',
                    'contentReviewRejected' => '內容審查拒絕',
                ],
                default => null,
            };

            if (empty($langAddContent)) {
                $packContent->update([
                    'lang_content' => $replaced,
                ]);

                continue;
            }

            // merge by key de-duplication
            $langNewContent = clone $newContent;
            foreach ($langAddContent as $key => $value) {
                if (!property_exists($newContent, $key)) {
                    $langNewContent->$key = $value;
                }
            }

            $packContent->lang_content = json_encode($langNewContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
            $packContent->save();
        }

        // code messages
        $code31305Messages = CodeMessage::where('plugin_fskey', 'Fresns')->where('code', 31305)->where('lang_tag', 'en')->first();
        if (empty($code31305Messages)) {
            CodeMessage::updateOrCreate([
                'plugin_fskey' => 'Fresns',
                'code' => '31305',
                'lang_tag' => 'en',
            ],
            [
                'message' => 'Read-only key are not entitled to request for the interface',
            ]);
            CodeMessage::updateOrCreate([
                'plugin_fskey' => 'Fresns',
                'code' => '31305',
                'lang_tag' => 'zh-Hans',
            ],
            [
                'message' => '只读密钥无权请求本接口',
            ]);
            CodeMessage::updateOrCreate([
                'plugin_fskey' => 'Fresns',
                'code' => '31305',
                'lang_tag' => 'zh-Hant',
            ],
            [
                'message' => '只讀密鑰無權請求本接口',
            ]);
        }

        if (! Schema::hasTable('user_extcredits_logs')) {
            Schema::create('user_extcredits_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id')->index('extcredits_log_user_id');
                $table->unsignedTinyInteger('extcredits_id')->index('extcredits_id');
                $table->unsignedTinyInteger('type');
                $table->unsignedInteger('amount');
                $table->unsignedInteger('opening_amount');
                $table->unsignedInteger('closing_amount');
                $table->string('plugin_fskey', 64);
                $table->text('remark')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();
                $table->softDeletes();
            });
        }

        try {
            Schema::table('account_wallet_logs', function (Blueprint $table) {
                $table->index('account_id', 'wallet_log_account_id');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('post_appends', function (Blueprint $table) {
                $table->dropColumn('is_allow');
                $table->dropColumn('allow_percentage');
                $table->dropColumn('allow_btn_name');
                $table->dropColumn('allow_plugin_fskey');
                $table->dropColumn('is_comment');
                $table->dropColumn('is_comment_public');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('post_logs', function (Blueprint $table) {
                $table->dropColumn('is_comment');
                $table->dropColumn('is_comment_public');
                $table->dropColumn('allow_json');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('post_appends', function (Blueprint $table) {
                $table->string('read_plugin_fskey', 64)->nullable()->after('can_delete');
                $table->string('read_btn_name', 64)->nullable()->after('can_delete');
                $table->unsignedTinyInteger('read_pre_percentage')->nullable()->after('can_delete');
                $table->unsignedTinyInteger('is_read_locked')->default(0)->after('can_delete');
                $table->unsignedTinyInteger('is_comment_private')->default(0)->after('user_list_plugin_fskey');
                $table->unsignedTinyInteger('is_comment_disabled')->default(0)->after('user_list_plugin_fskey');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('post_logs', function (Blueprint $table) {
                $table->unsignedTinyInteger('is_comment_private')->default(0)->after('is_anonymous');
                $table->unsignedTinyInteger('is_comment_disabled')->default(0)->after('is_anonymous');
                $table->json('read_json')->nullable()->after('map_json');
            });
        } catch (\Exception $e) {}

        try {
            Schema::rename('post_allows', 'post_auths');
        } catch (\Exception $e) {}

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
