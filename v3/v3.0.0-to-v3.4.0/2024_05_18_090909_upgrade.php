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
     * Upgrade to v3.2.0
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 3.2.0"]);

        if (Schema::hasColumn('archives', 'form_element')) {
            Schema::table('archives', function (Blueprint $table) {
                $table->renameColumn('form_element', 'form_type');
            });
        }

        if (Schema::hasColumn('archives', 'element_options')) {
            Schema::table('archives', function (Blueprint $table) {
                $table->renameColumn('element_options', 'form_options');
            });
        }

        if (Schema::hasColumn('archives', 'element_type')) {
            Schema::table('archives', function (Blueprint $table) {
                $table->dropColumn('element_type');
            });
        }

        if (Schema::hasColumn('archives', 'input_size')) {
            Schema::table('archives', function (Blueprint $table) {
                $table->dropColumn('input_size');
            });
        }

        if (Schema::hasColumn('archives', 'input_step')) {
            Schema::table('archives', function (Blueprint $table) {
                $table->dropColumn('input_step');
            });
        }

        if (Schema::hasColumn('archives', 'value_type')) {
            Schema::table('archives', function (Blueprint $table) {
                $table->dropColumn('value_type');
            });
        }

        if (! Schema::hasColumn('archives', 'is_tree_option')) {
            Schema::table('archives', function (Blueprint $table) {
                $table->boolean('is_tree_option')->default(0)->after('file_type');
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
