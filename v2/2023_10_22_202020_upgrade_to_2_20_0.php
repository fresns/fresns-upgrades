<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\AccountWalletLog;
use App\Models\Language;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v2.20.0
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 2.20.0"]);

        if (Schema::hasColumn('account_wallet_logs', 'is_enabled')) {
            DB::transaction(function () {
                AccountWalletLog::where('is_enabled', 0)->update(['is_enabled' => 4]);
                AccountWalletLog::where('is_enabled', 1)->update(['is_enabled' => 3]);
            });
        }

        if (Schema::hasColumn('account_wallet_logs', 'state')) {
            DB::transaction(function () {
                AccountWalletLog::where('state', 0)->update(['state' => 4]);
                AccountWalletLog::where('state', 1)->update(['state' => 3]);
            });
        }

        DB::transaction(function () {
            AccountWalletLog::where('type', 15)->update(['type' => 5]);
            AccountWalletLog::where('type', 16)->update(['type' => 6]);
            AccountWalletLog::where('type', 17)->update(['type' => 7]);
        });

        $langPackContents = Language::where('table_name', 'configs')->where('table_column', 'item_value')->where('table_key', 'language_pack_contents')->get();
        foreach ($langPackContents as $packContent) {
            $content = $packContent->lang_content;

            if (empty($content)) {
                continue;
            }

            $content = json_decode($content, true);

            switch ($packContent->lang_tag) {
                case 'en':
                    $content['walletLogType3'] = 'Transaction Income';
                    $content['walletLogType4'] = 'Reversal Expense';
                    $content['walletLogType5'] = 'Withdraw';
                    $content['walletLogType6'] = 'Freeze';
                    break;

                case 'zh-Hans':
                    $content['walletLogType3'] = '交易收入';
                    $content['walletLogType4'] = '撤回支出';
                    $content['walletLogType5'] = '提现';
                    $content['walletLogType6'] = '冻结';
                    break;

                case 'zh-Hant':
                    $content['walletLogType3'] = '交易收入';
                    $content['walletLogType4'] = '撤回支出';
                    $content['walletLogType5'] = '提現';
                    $content['walletLogType6'] = '凍結';
                    break;

                default:
                    continue 2;
            }

            $packContent->lang_content = json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
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
