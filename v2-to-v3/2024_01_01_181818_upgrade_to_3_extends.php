<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            'extends',
        ]);

        if (! Schema::hasColumn('archives', 'name')) {
            Schema::table('archives', function (Blueprint $table) {
                $table->json('description')->nullable()->after('app_fskey');
                $table->json('name')->nullable()->after('app_fskey');
                $table->renameColumn('rating', 'sort_order');
            });
        }

        if (! Schema::hasColumn('operations', 'name')) {
            Schema::table('operations', function (Blueprint $table) {
                $table->json('description')->nullable()->after('style');
                $table->json('name')->nullable()->after('style');
            });
        }

        if (! Schema::hasColumn('extends', 'name')) {
            Schema::table('extends', function (Blueprint $table) {
                $table->json('action_items')->nullable()->after('app_fskey');
                $table->json('content')->nullable()->after('app_fskey');
                $table->timestamp('ended_at')->nullable()->after('is_enabled');
                $table->renameColumn('position', 'view_position');
                $table->renameColumn('info_box_type', 'view_type');
                $table->renameColumn('cover_file_id', 'image_file_id');
                $table->renameColumn('cover_file_url', 'image_file_url');
                $table->renameColumn('parameter', 'url_parameter');
            });
        }

        if (! Schema::hasTable('extend_users')) {
            Schema::create('extend_users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('extend_id')->index('extend_id');
                $table->unsignedBigInteger('user_id')->index('extend_user_id');
                $table->string('action_key', 64)->nullable()->index('extend_action_key');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->nullable();
                $table->softDeletes();
            });
        }

        if (Schema::hasColumn('extend_usages', 'rating')) {
            Schema::table('extend_usages', function (Blueprint $table) {
                $table->renameColumn('rating', 'sort_order');
            });
        }

        if (! Schema::hasColumn('seo', 'title')) {
            Schema::table('seo', function (Blueprint $table) {
                $table->json('description')->nullable()->after('usage_id');
                $table->json('keywords')->nullable()->after('usage_id');
                $table->json('title')->nullable()->after('usage_id');
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
