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

        if (Schema::hasColumn('archives', 'name')) {
            Schema::table('archives', function (Blueprint $table) {
                $table->dropColumn('name');
                $table->dropColumn('description');
            });
        }

        if (Schema::hasColumn('operations', 'name')) {
            Schema::table('operations', function (Blueprint $table) {
                $table->dropColumn('name');
                $table->dropColumn('description');
            });
        }

        if (Schema::hasColumn('extends', 'title')) {
            Schema::table('extends', function (Blueprint $table) {
                $table->dropColumn('text_content');
                $table->dropColumn('text_is_markdown');
                $table->dropColumn('title');
                $table->dropColumn('title_color');
                $table->dropColumn('desc_primary');
                $table->dropColumn('desc_primary_color');
                $table->dropColumn('desc_secondary');
                $table->dropColumn('desc_secondary_color');
                $table->dropColumn('button_name');
                $table->dropColumn('button_color');
            });
        }

        if (Schema::hasColumn('seo', 'lang_tag')) {
            Schema::table('seo', function (Blueprint $table) {
                $table->dropColumn('lang_tag');
            });
        }

        if (Schema::hasColumn('seo', 'title')) {
            Schema::table('seo', function (Blueprint $table) {
                $table->dropColumn('title');
                $table->dropColumn('keywords');
                $table->dropColumn('description');
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
