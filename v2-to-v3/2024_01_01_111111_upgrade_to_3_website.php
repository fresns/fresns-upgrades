<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\Config;
use App\Utilities\AppUtility;
use App\Utilities\ConfigUtility;
use Fresns\PluginManager\Support\Process;
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
            'website configs',
        ]);

        $configs = Config::whereIn('item_key', [
            'webengine_status',
            'webengine_api_type',
            'webengine_api_host',
            'webengine_api_app_id',
            'webengine_api_app_secret',
            'webengine_key_id',
            'webengine_view_desktop',
            'webengine_view_mobile',
        ])->get();

        foreach ($configs as $config) {
            $itemKey = $config->item_key;

            $newItemKey = match ($itemKey) {
                'webengine_status' => 'website_engine_status',
                'webengine_api_type' => 'website_engine_api_type',
                'webengine_api_host' => 'website_engine_api_host',
                'webengine_api_app_id' => 'website_engine_api_app_id',
                'webengine_api_app_secret' => 'website_engine_api_app_key',
                'webengine_key_id' => 'website_engine_key_id',
                'webengine_view_desktop' => 'website_engine_view_desktop',
                'webengine_view_mobile' => 'website_engine_view_mobile',
                default => $itemKey,
            };

            $config->update([
                'item_key' => $newItemKey,
            ]);
        }

        ConfigUtility::addFresnsConfigItems([
            [
                'item_key' => 'website_geotag_path',
                'item_value' => 'geotags',
                'item_type' => 'string',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
            [
                'item_key' => 'website_geotag_detail_path',
                'item_value' => 'geotag',
                'item_type' => 'string',
                'is_multilingual' => 0,
                'is_api' => 1,
            ],
        ]);

        // composer remove fresns/web-engine
        $httpProxy = config('app.http_proxy');
        Process::run(<<<"SHELL"
            export http_proxy=$httpProxy https_proxy=$httpProxy
            echo http_proxy=\$http_proxy
            echo https_proxy=\$https_proxy
            echo "current user:" `whoami`
            echo "home path permission is:" `ls -ld ~`
            echo ""

            #test -f ~/.config/composer/composer.json && echo 1 || (mkdir -p ~/.config/composer && echo "{}" > ~/.config/composer/composer.json)
            #echo ""

            echo "global composer.json content": `cat ~/.config/composer/composer.json`
            echo ""

            echo "PATH:" `echo \$PATH`
            echo ""

            echo "php:" `which php` "\n version" `php -v`
            echo "composer:" `which composer` "\n version" `composer --version`
            echo "git:" `which git` "\n version" `git --version`
            echo ""

            # install command
            composer diagnose
            composer remove fresns/web-engine
        SHELL);
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
};
