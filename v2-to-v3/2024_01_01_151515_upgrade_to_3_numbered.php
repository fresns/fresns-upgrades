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

        FileDownload::where('object_type', 4)->update(['object_type' => 5]);

        Mention::where('mention_type', 4)->update(['mention_type' => 5]);

        DomainLinkUsage::where('usage_type', 4)->update(['usage_type' => 5]);

        HashtagUsage::where('usage_type', 4)->update(['usage_type' => 5]);

        OperationUsage::where('usage_type', 4)->update(['usage_type' => 5]);

        UserLike::where('like_type', 4)->update(['like_type' => 5]);

        UserFollow::where('follow_type', 4)->update(['follow_type' => 5]);

        Notification::where('action_object', 4)->update(['action_object' => 5]);
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
};
