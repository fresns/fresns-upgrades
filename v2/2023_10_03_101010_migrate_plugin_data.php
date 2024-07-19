<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Helpers\CacheHelper;
use App\Models\Plugin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run fresns migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('plugins', 'is_standalone')) {
            $plugins = Plugin::get();

            foreach ($plugins as $plugin) {
                if (in_array($plugin->fskey, [
                    'WeChat',
                    'MomentsWeChat',
                    'YouRanSNS',
                ])) {
                    $plugin->update([
                        'is_standalone' => true,
                    ]);

                    continue;
                }

                $plugin->update([
                    'is_standalone' => false,
                ]);
            }
        }

        DB::table('plugins')->update([
            'type' => 0,
        ]);

        // plugin config file
        $pluginFilePath = base_path('config/plugins.php');
        if (file_exists($pluginFilePath)) {
            unlink($pluginFilePath);
        }

        // theme config file
        $themeFilePath = base_path('config/themes.php');
        if (file_exists($themeFilePath)) {
            unlink($themeFilePath);
        }

        // market config file
        $marketFilePath = base_path('config/markets.php');
        if (file_exists($marketFilePath)) {
            unlink($marketFilePath);
        }

        Artisan::call('market:remove-plugin', [
            'fskey' => 'FresnsEngine',
            '--cleardata' => true,
        ]);

        CacheHelper::clearAllCache();
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
};
