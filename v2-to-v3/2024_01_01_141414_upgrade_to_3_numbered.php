<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\DomainLinkUsage;
use App\Models\FileDownload;
use App\Models\HashtagUsage;
use App\Models\Mention;
use App\Models\Notification;
use App\Models\OperationUsage;
use App\Models\UserFollow;
use App\Models\UserLike;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;

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
            'numbered',
        ]);

        FileDownload::where('object_type', 9)->update(['object_type' => 10]);
        FileDownload::where('object_type', 5)->update(['object_type' => 6]);

        Mention::where('mention_type', 5)->update(['mention_type' => 6]);

        DomainLinkUsage::where('usage_type', 5)->update(['usage_type' => 6]);

        HashtagUsage::where('usage_type', 5)->update(['usage_type' => 6]);

        OperationUsage::where('usage_type', 5)->update(['usage_type' => 6]);

        UserLike::where('like_type', 5)->update(['like_type' => 6]);

        UserFollow::where('follow_type', 5)->update(['follow_type' => 6]);

        Notification::where('action_object', 5)->update(['action_object' => 6]);
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
};
