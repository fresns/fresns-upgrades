<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            'messages',
        ]);

        // notifications
        if (Schema::hasColumn('notifications', 'nmid')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropColumn('nmid');
            });
        }
        if (! Schema::hasColumn('notifications', 'nmid')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('nmid', 32)->nullable()->after('id');
            });
        }
        DB::table('notifications')->whereNull('nmid')->select('id')->chunkById(100, function ($notifications) {
            foreach ($notifications as $notification) {
                DB::table('notifications')->where('id', $notification->id)->update(['nmid' => Str::random(16)]);
            }
        });
        if (Schema::hasColumn('notifications', 'nmid')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('nmid', 32)->unique()->change();
            });
        }

        // conversation_messages
        if (Schema::hasColumn('conversation_messages', 'cmid')) {
            Schema::table('conversation_messages', function (Blueprint $table) {
                $table->dropColumn('cmid');
            });
        }
        if (! Schema::hasColumn('conversation_messages', 'cmid')) {
            Schema::table('conversation_messages', function (Blueprint $table) {
                $table->string('cmid', 32)->nullable()->after('id');
            });
        }
        DB::table('conversation_messages')->whereNull('cmid')->select('id')->chunkById(100, function ($conversationMessages) {
            foreach ($conversationMessages as $message) {
                DB::table('conversation_messages')->where('id', $message->id)->update(['cmid' => Str::random(16)]);
            }
        });
        if (Schema::hasColumn('conversation_messages', 'cmid')) {
            Schema::table('conversation_messages', function (Blueprint $table) {
                $table->string('cmid', 32)->unique()->change();
            });
        }

        // notifications content
        if (Schema::hasColumn('notifications', 'content')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropColumn('content');
                $table->dropColumn('is_multilingual');
            });
        }

        if (! Schema::hasColumn('notifications', 'content')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->json('content')->nullable()->after('user_id');
            });
        }

        if (Schema::hasColumn('notifications', 'action_object')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->renameColumn('action_object', 'action_target');
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
