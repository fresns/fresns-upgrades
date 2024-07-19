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
use Illuminate\Support\Str;

class UpgradeTo28a extends Migration
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

        if (Schema::hasColumn('accounts', 'is_enable')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('account_connects', 'is_enable')) {
            Schema::table('account_connects', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('account_wallets', 'is_enable')) {
            Schema::table('account_wallets', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('account_wallet_logs', 'is_enable')) {
            Schema::table('account_wallet_logs', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('archives', 'is_enable')) {
            Schema::table('archives', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('comments', 'is_enable')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('conversation_messages', 'is_enable')) {
            Schema::table('conversation_messages', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('domains', 'is_enable')) {
            Schema::table('domains', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('domain_links', 'is_enable')) {
            Schema::table('domain_links', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('extends', 'is_enable')) {
            Schema::table('extends', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('files', 'is_enable')) {
            Schema::table('files', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('groups', 'is_enable')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('hashtags', 'is_enable')) {
            Schema::table('hashtags', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('operations', 'is_enable')) {
            Schema::table('operations', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('plugins', 'is_enable')) {
            Schema::table('plugins', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('plugin_usages', 'is_enable')) {
            Schema::table('plugin_usages', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('posts', 'is_enable')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('roles', 'is_enable')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('session_keys', 'is_enable')) {
            Schema::table('session_keys', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('stickers', 'is_enable')) {
            Schema::table('stickers', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('users', 'is_enable')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('user_follows', 'is_enable')) {
            Schema::table('user_follows', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        if (Schema::hasColumn('verify_codes', 'is_enable')) {
            Schema::table('verify_codes', function (Blueprint $table) {
                $table->renameColumn('is_enable', 'is_enabled');
            });
        }

        $languageMenus = Config::where('item_key', 'language_menus')->first();
        if ($languageMenus?->item_value) {
            $menus = json_encode($languageMenus->item_value);
            $replaced = Str::replace('"isEnable"', '"isEnabled"', $menus);
            $languageMenus->update([
                'item_value' => $replaced,
            ]);
        }

        $verifycode_template1 = Config::where('item_key', 'verifycode_template1')->first();
        if ($verifycode_template1?->item_value) {
            $verifycode_template1_value = json_encode($verifycode_template1->item_value);
            $new_verifycode_template1_value = Str::replace('"isEnable"', '"isEnabled"', $verifycode_template1_value);
            $verifycode_template1->update([
                'item_value' => $new_verifycode_template1_value,
            ]);
        }

        $verifycode_template2 = Config::where('item_key', 'verifycode_template2')->first();
        if ($verifycode_template2?->item_value) {
            $verifycode_template2_value = json_encode($verifycode_template2->item_value);
            $new_verifycode_template2_value = Str::replace('"isEnable"', '"isEnabled"', $verifycode_template2_value);
            $verifycode_template2->update([
                'item_value' => $new_verifycode_template2_value,
            ]);
        }

        $verifycode_template3 = Config::where('item_key', 'verifycode_template3')->first();
        if ($verifycode_template3?->item_value) {
            $verifycode_template3_value = json_encode($verifycode_template3->item_value);
            $new_verifycode_template3_value = Str::replace('"isEnable"', '"isEnabled"', $verifycode_template3_value);
            $verifycode_template3->update([
                'item_value' => $new_verifycode_template3_value,
            ]);
        }

        $verifycode_template4 = Config::where('item_key', 'verifycode_template4')->first();
        if ($verifycode_template4?->item_value) {
            $verifycode_template4_value = json_encode($verifycode_template4->item_value);
            $new_verifycode_template4_value = Str::replace('"isEnable"', '"isEnabled"', $verifycode_template4_value);
            $verifycode_template4->update([
                'item_value' => $new_verifycode_template4_value,
            ]);
        }

        $verifycode_template5 = Config::where('item_key', 'verifycode_template5')->first();
        if ($verifycode_template5?->item_value) {
            $verifycode_template5_value = json_encode($verifycode_template5->item_value);
            $new_verifycode_template5_value = Str::replace('"isEnable"', '"isEnabled"', $verifycode_template5_value);
            $verifycode_template5->update([
                'item_value' => $new_verifycode_template5_value,
            ]);
        }

        $verifycode_template6 = Config::where('item_key', 'verifycode_template6')->first();
        if ($verifycode_template6?->item_value) {
            $verifycode_template6_value = json_encode($verifycode_template6->item_value);
            $new_verifycode_template6_value = Str::replace('"isEnable"', '"isEnabled"', $verifycode_template6_value);
            $verifycode_template6->update([
                'item_value' => $new_verifycode_template6_value,
            ]);
        }

        $verifycode_template7 = Config::where('item_key', 'verifycode_template7')->first();
        if ($verifycode_template7?->item_value) {
            $verifycode_template7_value = json_encode($verifycode_template7->item_value);
            $new_verifycode_template7_value = Str::replace('"isEnable"', '"isEnabled"', $verifycode_template7_value);
            $verifycode_template7->update([
                'item_value' => $new_verifycode_template7_value,
            ]);
        }

        $verifycode_template8 = Config::where('item_key', 'verifycode_template8')->first();
        if ($verifycode_template8?->item_value) {
            $verifycode_template8_value = json_encode($verifycode_template8->item_value);
            $new_verifycode_template8_value = Str::replace('"isEnable"', '"isEnabled"', $verifycode_template8_value);
            $verifycode_template8->update([
                'item_value' => $new_verifycode_template8_value,
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
}
