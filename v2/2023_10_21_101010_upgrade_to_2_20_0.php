<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\AccountWalletLog;
use App\Models\Config;
use App\Models\Language;
use App\Utilities\AppUtility;
use App\Utilities\ArrUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            Schema::table('account_wallet_logs', function (Blueprint $table) {
                $table->renameColumn('is_enabled', 'state');
            });
        }

        if (! Schema::hasColumn('account_wallet_logs', 'success_at')) {
            Schema::table('account_wallet_logs', function (Blueprint $table) {
                $table->timestamp('success_at')->nullable()->after('more_json');
            });
        }

        // Transaction type temporary data
        DB::transaction(function () {
            AccountWalletLog::where('type', 4)->update(['type' => 15]);
            AccountWalletLog::where('type', 5)->update(['type' => 16]);
            AccountWalletLog::where('type', 6)->update(['type' => 17]);
        });

        // lang pack add key
        $languagePack = Config::where('item_key', 'language_pack')->first();
        if ($languagePack) {
            $packData = $languagePack->item_value;

            $addPackKeys = [
                [
                    'name' => 'walletLogCode',
                    'canDelete' => false,
                ],
                [
                    'name' => 'walletLogType7',
                    'canDelete' => false,
                ],
                [
                    'name' => 'walletLogType8',
                    'canDelete' => false,
                ],
                [
                    'name' => 'walletLogState1',
                    'canDelete' => false,
                ],
                [
                    'name' => 'walletLogState2',
                    'canDelete' => false,
                ],
                [
                    'name' => 'walletLogState3',
                    'canDelete' => false,
                ],
                [
                    'name' => 'walletLogState4',
                    'canDelete' => false,
                ],
                [
                    'name' => 'walletLogState5',
                    'canDelete' => false,
                ],
            ];

            // merge by name de-duplication
            $mergedData = $packData;
            foreach ($addPackKeys as $addPackKey) {
                $nameExists = false;
                foreach ($packData as $packItem) {
                    if ($packItem['name'] === $addPackKey['name']) {
                        $nameExists = true;
                        break;
                    }
                }

                if (! $nameExists) {
                    $mergedData[] = $addPackKey;
                }
            }

            // modify name
            $mergedData = ArrUtility::editValue($mergedData, 'name', 'walletLogStatus', 'walletLogState');

            $languagePack->item_value = $mergedData;
            $languagePack->save();
        }

        $langPackContents = Language::where('table_name', 'configs')->where('table_column', 'item_value')->where('table_key', 'language_pack_contents')->get();
        foreach ($langPackContents as $packContent) {
            $content = $packContent->lang_content;
            if (empty($content)) {
                continue;
            }

            $content = (object) json_decode($content, true);

            $content = ArrUtility::editKey($content, 'walletLogStatus', 'walletLogState');

            $langAddContent = match ($packContent->lang_tag) {
                'en' => [
                    'walletLogCode' => 'Transaction Code',
                    'walletLogType7' => 'Transaction Expense',
                    'walletLogType8' => 'Reversal Income',
                    'walletLogState1' => 'Pending',
                    'walletLogState2' => 'Processing',
                    'walletLogState3' => 'Success',
                    'walletLogState4' => 'Failed',
                    'walletLogState5' => 'Reversed',
                ],
                'zh-Hans' => [
                    'walletLogCode' => '交易凭证',
                    'walletLogType7' => '交易支出',
                    'walletLogType8' => '撤回收入',
                    'walletLogState1' => '等待处理',
                    'walletLogState2' => '处理中',
                    'walletLogState3' => '交易成功',
                    'walletLogState4' => '交易失败',
                    'walletLogState5' => '交易撤回',
                ],
                'zh-Hant' => [
                    'walletLogCode' => '交易憑證',
                    'walletLogType7' => '交易支出',
                    'walletLogType8' => '撤回收入',
                    'walletLogState1' => '等待處理',
                    'walletLogState2' => '處理中',
                    'walletLogState3' => '交易成功',
                    'walletLogState4' => '交易失敗',
                    'walletLogState5' => '交易撤回',
                ],
                default => null,
            };

            if (empty($langAddContent)) {
                continue;
            }

            // merge by key de-duplication
            $langNewContent = clone $content;
            foreach ($langAddContent as $key => $value) {
                if (!property_exists($content, $key)) {
                    $langNewContent->$key = $value;
                }
            }

            $packContent->lang_content = json_encode($langNewContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
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
