<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\Sticker;
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
            'stickers',
        ]);

        if (Schema::hasColumn('stickers', 'rating')) {
            Schema::table('stickers', function (Blueprint $table) {
                $table->renameColumn('rating', 'sort_order');
            });
        }
        if (Schema::hasColumn('stickers', 'name')) {
            Schema::table('stickers', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
        if (! Schema::hasColumn('stickers', 'name')) {
            Schema::table('stickers', function (Blueprint $table) {
                $table->json('name')->nullable()->after('code');
            });
        }

        $stickers = Sticker::where('type', Sticker::TYPE_GROUP)->get();

        foreach ($stickers as $sticker) {
            $newName = match ($sticker->code) {
                'default' => '{"en":"Default","zh-Hans":"默认","zh-Hant":"默認"}',
                'coolmonkey' => '{"en":"Cool Monkey","zh-Hans":"酷猴","zh-Hant":"酷猴"}',
                'grapeman' => '{"en":"Grape Man","zh-Hans":"呆呆男","zh-Hant":"呆呆男"}',
                'face' => '{"en":"Face","zh-Hans":"小黄脸","zh-Hant":"小黃臉"}',
                default => null,
            };

            if (empty($newName)) {
                continue;
            }

            $sticker->update([
                'name' => json_decode($newName, true),
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
};
