<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Helpers\CacheHelper;
use App\Models\CodeMessage;
use App\Models\Config;
use App\Models\Language;
use App\Utilities\AppUtility;
use App\Utilities\ArrUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpgradeTo22 extends Migration
{
    /**
     * Run fresns migrations.
     *
     * Upgrade to 22 (fresns v2.8.0)
     */
    public function up(): void
    {
        $currentVersion = AppUtility::currentVersion()['version'];
        info('Migration: ', ["2.8.0 => {$currentVersion}"]);

        if (version_compare('2.8.0', $currentVersion) == -1) {
            return;
        }

        if (! Schema::hasColumn('posts', 'parent_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->unsignedBigInteger('parent_id')->default(0)->index('post_parent_id')->after('pid');
                $table->unsignedInteger('post_count')->default(0)->after('comment_block_count');
            });
        }

        if (Schema::hasColumn('posts', 'map_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('map_id');
            });
            Schema::table('post_appends', function (Blueprint $table) {
                switch (config('database.default')) {
                    case 'pgsql':
                        $table->jsonb('more_json')->nullable()->after('comment_btn_plugin_unikey');
                        break;

                    case 'sqlsrv':
                        $table->nvarchar('more_json', 'max')->nullable()->after('comment_btn_plugin_unikey');
                        break;

                    default:
                        $table->json('more_json')->nullable()->after('comment_btn_plugin_unikey');
                }
                $table->unsignedTinyInteger('map_id')->nullable()->after('map_json');
                $table->dropColumn('ip_location');
                $table->dropColumn('map_scale');
                $table->dropColumn('map_city');
                $table->dropColumn('map_poi');
            });
        }

        if (! Schema::hasColumn('post_logs', 'parent_post_id')) {
            Schema::table('post_logs', function (Blueprint $table) {
                $table->unsignedBigInteger('parent_post_id')->nullable()->after('post_id');
            });
        }

        if (Schema::hasColumn('comments', 'map_id')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropColumn('map_id');
            });
            Schema::table('comment_appends', function (Blueprint $table) {
                switch (config('database.default')) {
                    case 'pgsql':
                        $table->jsonb('more_json')->nullable()->after('btn_style');
                        break;

                    case 'sqlsrv':
                        $table->nvarchar('more_json', 'max')->nullable()->after('btn_style');
                        break;

                    default:
                        $table->json('more_json')->nullable()->after('btn_style');
                }
                $table->unsignedTinyInteger('map_id')->nullable()->after('map_json');
                $table->dropColumn('ip_location');
                $table->dropColumn('map_scale');
                $table->dropColumn('map_city');
                $table->dropColumn('map_poi');
            });
        }

        // index
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index('nickname');
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('user_roles', function (Blueprint $table) {
                $table->index('user_id', 'role_user_id');
                $table->index('role_id', 'user_role_id');
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('file_usages', function (Blueprint $table) {
                $table->dropIndex('table_id');
                $table->dropIndex('table_key');
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('file_downloads', function (Blueprint $table) {
                $table->index('file_id', 'download_file_id');
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('posts', function (Blueprint $table) {
                $table->index('user_id', 'post_user_id');
                $table->index('group_id', 'post_group_id');
                $table->index('sticky_state', 'post_sticky_state');
                $table->index('digest_state', 'post_digest_state');
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('post_appends', function (Blueprint $table) {
                $table->dropIndex('post_continent_country_region_city');
            });
            Schema::table('post_appends', function (Blueprint $table) {
                $table->index('map_region_code', 'post_map_region_code');
                $table->index('map_city_code', 'post_map_city_code');
                $table->index('map_poi_id', 'post_map_poi_id');
                $table->index(['map_continent_code', 'map_country_code'], 'post_continent_country');
            });
            Schema::table('post_logs', function (Blueprint $table) {
                $table->index('user_id', 'post_log_user_id');
                $table->index('post_id', 'post_log_post_id');
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('comments', function (Blueprint $table) {
                $table->index('post_id', 'comment_post_id');
                $table->index('top_parent_id', 'comment_top_parent_id');
                $table->index('parent_id', 'comment_parent_id');
                $table->index('user_id', 'comment_user_id');
                $table->index('digest_state', 'comment_digest_state');
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('comment_appends', function (Blueprint $table) {
                $table->dropIndex('comment_continent_country_region_city');
            });
            Schema::table('comment_appends', function (Blueprint $table) {
                $table->index('map_region_code', 'comment_map_region_code');
                $table->index('map_city_code', 'comment_map_city_code');
                $table->index('map_poi_id', 'comment_map_poi_id');
                $table->index(['map_continent_code', 'map_country_code'], 'comment_continent_country');
            });
            Schema::table('comment_logs', function (Blueprint $table) {
                $table->index('user_id', 'comment_log_user_id');
                $table->index('comment_id', 'comment_log_comment_id');
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('groups', function (Blueprint $table) {
                $table->index('parent_id', 'group_parent_id');
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('group_admins', function (Blueprint $table) {
                $table->index('group_id', 'admin_group_id');
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('hashtags', function (Blueprint $table) {
                $table->unique('name', 'hashtag_name');
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('session_logs', function (Blueprint $table) {
                $table->index('plugin_unikey', 'log_plugin_unikey');
                $table->index('type', 'log_type');
                $table->index('app_id', 'log_app_id');
                $table->index('account_id', 'log_account_id');
                $table->index('user_id', 'log_user_id');
            });
            Schema::table('session_tokens', function (Blueprint $table) {
                $table->index('app_id', 'token_app_id');
            });
        } catch (\Exception $e) {}
        try {
            Schema::table('verify_codes', function (Blueprint $table) {
                $table->dropIndex('account');
                $table->index(['type', 'account', 'code'], 'account_verify_code');
            });
        } catch (\Exception $e) {}

        // configs
        $storages = Config::where('item_key', 'storages')->first();
        $storages?->update([
            'item_value' => '[{"id":"1","name":"Unknown"},{"id":"2","name":"Local"},{"id":"3","name":"WebDAV"},{"id":"4","name":"Amazon S3"},{"id":"5","name":"Backblaze B2"},{"id":"6","name":"Dropbox"},{"id":"7","name":"Microsoft OneDrive"},{"id":"8","name":"Microsoft Azure Storage Service"},{"id":"9","name":"Google Cloud Storage"},{"id":"10","name":"Google Drive"},{"id":"11","name":"OpenStack Swift"},{"id":"12","name":"Box"},{"id":"13","name":"Cloudinary"},{"id":"14","name":"DigitalOcean Object Storage"},{"id":"15","name":"Linode Object Storage"},{"id":"16","name":"Vultr Object Storage"},{"id":"17","name":"七牛云 Kodo"},{"id":"18","name":"又拍云 USS"},{"id":"19","name":"阿里云 OSS"},{"id":"20","name":"腾讯云 COS"},{"id":"21","name":"火山引擎 TOS"},{"id":"22","name":"网易蜂巢 NOS"},{"id":"23","name":"UCloud UFile"},{"id":"24","name":"华为云 OBS"},{"id":"25","name":"金山云 KS3"},{"id":"26","name":"华云数据 COS"},{"id":"27","name":"天翼云 CT-OOS"},{"id":"28","name":"保利威 Polyv"},{"id":"29","name":"Fastly"}]',
        ]);
        $connects = Config::where('item_key', 'connects')->first();
        $connects?->update([
            'item_value' => '[{"id":"1","name":"Other"},{"id":"2","name":"Fresns"},{"id":"3","name":"SSO"},{"id":"4","name":"GitHub"},{"id":"5","name":"GitLab"},{"id":"6","name":"Bitbucket"},{"id":"7","name":"Google"},{"id":"8","name":"Facebook"},{"id":"9","name":"Instagram"},{"id":"10","name":"Twitter"},{"id":"11","name":"Discord"},{"id":"12","name":"Telegram"},{"id":"13","name":"Apple"},{"id":"14","name":"Microsoft"},{"id":"15","name":"LinkedIn"},{"id":"16","name":"PayPal"},{"id":"17","name":"Slack"},{"id":"18","name":"Netlify"},{"id":"19","name":"LINE"},{"id":"20","name":"KakaoTalk"},{"id":"21","name":"Lark"},{"id":"22","name":"Steam"},{"id":"23","name":"WeChat Open Platform (UnionID)"},{"id":"24","name":"WeChat Official Accounts Platform"},{"id":"25","name":"WeChat Mini Program"},{"id":"26","name":"WeChat Mobile Application"},{"id":"27","name":"WeChat Website Application"},{"id":"28","name":"WeCom"},{"id":"29","name":"Tencent QQ"},{"id":"30","name":"Gitee"},{"id":"31","name":"Weibo"},{"id":"32","name":"Alipay"},{"id":"33","name":"ByteDance"}]',
        ]);

        $mapService = Config::where('item_key', 'map_service')->first();
        if (! $mapService) {
            $fresnsItems = Config::where('item_key', 'fresns_items')->first();

            if ($fresnsItems) {
                $fresnsItems->item_key = 'map_service';
                $fresnsItems->item_value = null;
                $fresnsItems->item_type = 'plugin';
                $fresnsItems->item_tag = 'extends';
                $fresnsItems->is_multilingual = 0;
                $fresnsItems->is_custom = 0;
                $fresnsItems->is_api = 1;
                $fresnsItems->save();
            } else {
                $newConfig = new Config;
                $newConfig->item_key = 'map_service';
                $newConfig->item_value = null;
                $newConfig->item_type = 'plugin';
                $newConfig->item_tag = 'extends';
                $newConfig->is_multilingual = 0;
                $newConfig->is_custom = 0;
                $newConfig->is_api = 1;
                $newConfig->save();
            }
        }

        $nicknameUnique = Config::where('item_key', 'nickname_unique')->first();
        if (! $nicknameUnique) {
            $packagistMirrors = Config::where('item_key', 'packagist_mirrors')->first();

            if ($packagistMirrors) {
                $packagistMirrors->item_key = 'nickname_unique';
                $packagistMirrors->item_value = 'false';
                $packagistMirrors->item_type = 'boolean';
                $packagistMirrors->item_tag = 'users';
                $packagistMirrors->is_multilingual = 0;
                $packagistMirrors->is_custom = 0;
                $packagistMirrors->is_api = 1;
                $packagistMirrors->save();
            } else {
                $newConfig = new Config;
                $newConfig->item_key = 'nickname_unique';
                $newConfig->item_value = 'false';
                $newConfig->item_type = 'boolean';
                $newConfig->item_tag = 'users';
                $newConfig->is_multilingual = 0;
                $newConfig->is_custom = 0;
                $newConfig->is_api = 1;
                $newConfig->save();
            }
        }

        $hashtagLength = Config::where('item_key', 'hashtag_length')->first();
        if (! $hashtagLength) {
            $newConfig = new Config;
            $newConfig->item_key = 'hashtag_length';
            $newConfig->item_value = '20';
            $newConfig->item_type = 'number';
            $newConfig->item_tag = 'interactions';
            $newConfig->is_multilingual = 0;
            $newConfig->is_custom = 0;
            $newConfig->is_api = 0;
            $newConfig->save();
        }

        $hashtagRegexp = Config::where('item_key', 'hashtag_regexp')->first();
        if (! $hashtagRegexp) {
            $newConfig = new Config;
            $newConfig->item_key = 'hashtag_regexp';
            $newConfig->item_value = '{"space":"/#[\\\\p{L}\\\\p{N}\\\\p{M}]+[^\\\\n\\\\p{P}\\\\s]/u","hash":"/#[\\\\p{L}\\\\p{N}\\\\p{M}]+[^\\\\n\\\\p{P}]#/u"}';
            $newConfig->item_type = 'object';
            $newConfig->item_tag = 'interactions';
            $newConfig->is_multilingual = 0;
            $newConfig->is_custom = 0;
            $newConfig->is_api = 0;
            $newConfig->save();
        }

        // code messages
        $code35111Messages = CodeMessage::where('plugin_unikey', 'Fresns')->where('code', 35111)->where('lang_tag', 'en')->first();
        if (empty($code35111Messages)) {
            CodeMessage::updateOrCreate([
                'plugin_unikey' => 'Fresns',
                'code' => '35111',
                'lang_tag' => 'en',
            ],
            [
                'message' => 'That nickname has been taken. Please choose another.',
            ]);
            CodeMessage::updateOrCreate([
                'plugin_unikey' => 'Fresns',
                'code' => '35111',
                'lang_tag' => 'zh-Hans',
            ],
            [
                'message' => '昵称已被使用',
            ]);
            CodeMessage::updateOrCreate([
                'plugin_unikey' => 'Fresns',
                'code' => '35111',
                'lang_tag' => 'zh-Hant',
            ],
            [
                'message' => '暱稱已被使用',
            ]);
        }

        // lang pack add key
        $languagePack = Config::where('item_key', 'language_pack')->first();
        if ($languagePack) {
            $packData = $languagePack->item_value;

            $addPackKeys = [
                [
                    'name' => 'retry',
                    'canDelete' => false,
                ],
                [
                    'name' => 'reselect',
                    'canDelete' => false,
                ],
                [
                    'name' => 'about',
                    'canDelete' => false,
                ],
                [
                    'name' => 'accountConnectEmpty',
                    'canDelete' => false,
                ],
                [
                    'name' => 'accountConnectLinked',
                    'canDelete' => false,
                ],
                [
                    'name' => 'accountConnectCreateNew',
                    'canDelete' => false,
                ],
                [
                    'name' => 'accountRealName',
                    'canDelete' => false,
                ],
                [
                    'name' => 'editorDraftSelect',
                    'canDelete' => false,
                ],
            ];

            // merge by name de-duplication
            $mergedData = $packData;
            foreach ($addPackKeys as $addPackKey) {
                $nameExists = false;
                foreach ($packData as $packItem) {
                    if ($packItem['name'] === $addPackKey['name']) {
                        $nameExists = true;
                        break;
                    }
                }

                if (!$nameExists) {
                    $mergedData[] = $addPackKey;
                }
            }

            // modify name
            $mergedData = ArrUtility::editValue($mergedData, 'name', 'editorCreate', 'editorDraftCreate');
            $mergedData = ArrUtility::editValue($mergedData, 'name', 'choose', 'select');
            $mergedData = ArrUtility::editValue($mergedData, 'name', 'editorNoChooseGroup', 'editorNoSelectGroup');

            $languagePack->item_value = $mergedData;
            $languagePack->save();
        }

        // lang pack add content
        $langPackContents = Language::where('table_name', 'configs')->where('table_column', 'item_value')->where('table_key', 'language_pack_contents')->get();
        foreach ($langPackContents as $packContent) {
            $content = (object) json_decode($packContent->lang_content, true);

            // modify key
            $content = ArrUtility::editKey($content, 'editorCreate', 'editorDraftCreate');
            $content = ArrUtility::editKey($content, 'choose', 'select');
            $content = ArrUtility::editKey($content, 'editorNoChooseGroup', 'editorNoSelectGroup');

            $langAddContent = match ($packContent->lang_tag) {
                'en' => [
                    'retry' => 'Retry',
                    'reselect' => 'Reselect',
                    'about' => 'About',
                    'accountConnectEmpty' => 'No account linkage information found',
                    'accountConnectLinked' => 'I have an account linked to a community',
                    'accountConnectCreateNew' => 'No account, create a new account',
                    'accountRealName' => 'Account Real Name',
                    'editorDraftSelect' => 'Select a draft from the draft box',
                ],
                'zh-Hans' => [
                    'retry' => '重试',
                    'reselect' => '重选',
                    'about' => '关于',
                    'accountConnectEmpty' => '未查询到账号关联信息',
                    'accountConnectLinked' => '我有账号，绑定关联',
                    'accountConnectCreateNew' => '没有账号，创建新账号',
                    'accountRealName' => '实名认证',
                    'editorDraftSelect' => '从草稿箱中选择一篇草稿',
                ],
                'zh-Hant' => [
                    'retry' => '重試',
                    'reselect' => '重選',
                    'about' => '關於',
                    'accountConnectEmpty' => '未查詢到賬號關聯信息',
                    'accountConnectLinked' => '我有賬號，綁定關聯',
                    'accountConnectCreateNew' => '沒有賬號，創建新賬號',
                    'accountRealName' => '實名認證',
                    'editorDraftSelect' => '從草稿箱中選擇一篇草稿',
                ],
                default => null,
            };

            if (empty($langAddContent)) {
                continue;
            }

            // merge by key de-duplication
            $langNewContent = clone $content;
            foreach ($langAddContent as $key => $value) {
                if (!property_exists($content, $key)) {
                    $langNewContent->$key = $value;
                }
            }

            $packContent->lang_content = json_encode($langNewContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
            $packContent->save();
        }

        CacheHelper::clearAllCache();
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        //
    }
}
