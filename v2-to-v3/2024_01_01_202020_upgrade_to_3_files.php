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
            'files',
        ]);

        if (Schema::hasColumn('files', 'more_json')) {
            Schema::table('files', function (Blueprint $table) {
                $table->renameColumn('more_json', 'more_info');
            });
        }

        if (Schema::hasColumn('files', 'is_sensitive')) {
            Schema::table('files', function (Blueprint $table) {
                $table->dropColumn('is_sensitive');
            });
        }

        if (Schema::hasColumn('files', 'image_handle_position')) {
            Schema::table('files', function (Blueprint $table) {
                $table->dropColumn('image_handle_position');
            });
        }

        if (! Schema::hasColumn('files', 'warning_type')) {
            Schema::table('files', function (Blueprint $table) {
                $table->unsignedTinyInteger('warning_type')->default(1)->after('original_path');
            });
        }

        if (Schema::hasColumn('file_usages', 'rating')) {
            Schema::table('file_usages', function (Blueprint $table) {
                $table->renameColumn('rating', 'sort_order');
            });
        }

        if (Schema::hasColumn('file_downloads', 'object_type')) {
            Schema::table('file_downloads', function (Blueprint $table) {
                $table->renameColumn('object_type', 'target_type');
                $table->renameColumn('object_id', 'target_id');
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
