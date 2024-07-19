<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Utilities\AppUtility;
use App\Utilities\ConfigUtility;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to v2.21.0
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["{$currentVersion} >> 2.21.0"]);

        ConfigUtility::addFresnsConfigItems([
            [
                'item_key' => 'post_delete',
                'item_value' => 'true',
                'item_type' => 'boolean',
                'item_tag' => 'postEditor',
                'is_multilingual' => 0,
                'is_custom' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'post_delete_sticky_limit',
                'item_value' => 'false',
                'item_type' => 'boolean',
                'item_tag' => 'postEditor',
                'is_multilingual' => 0,
                'is_custom' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'post_delete_digest_limit',
                'item_value' => 'false',
                'item_type' => 'boolean',
                'item_tag' => 'postEditor',
                'is_multilingual' => 0,
                'is_custom' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'comment_delete',
                'item_value' => 'true',
                'item_type' => 'boolean',
                'item_tag' => 'commentEditor',
                'is_multilingual' => 0,
                'is_custom' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'comment_delete_sticky_limit',
                'item_value' => 'false',
                'item_type' => 'boolean',
                'item_tag' => 'commentEditor',
                'is_multilingual' => 0,
                'is_custom' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'comment_delete_digest_limit',
                'item_value' => 'false',
                'item_type' => 'boolean',
                'item_tag' => 'commentEditor',
                'is_multilingual' => 0,
                'is_custom' => 0,
                'is_api' => 1,
            ],
        ]);
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
};
