<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Utilities;

use App\Models\CodeMessage;
use App\Models\Config;
use App\Models\Language;
use Illuminate\Support\Facades\DB;

class UpgradeUtility
{
    // fresns v2.0.0-beta.2
    public static function upgradeTo2(): bool
    {
        $packagistMirrors = Config::where('item_key', 'packagist_mirrors')->first();
        $itemValue = json_decode('[{"name":"Global, CloudFlare","repo":"https://packagist.pages.dev"},{"name":"Africa, South Africa","repo":"https://packagist.co.za"},{"name":"Asia, China Tencent","repo":"https://mirrors.tencent.com/composer/"},{"name":"Asia, India","repo":"https://packagist.in"},{"name":"Asia, Indonesia","repo":"https://packagist.phpindonesia.id"},{"name":"Asia, Japan","repo":"https://packagist.jp"},{"name":"Asia, South Korea","repo":"https://packagist.kr"},{"name":"Asia, Thailand","repo":"https://packagist.mycools.in.th/"},{"name":"Asia, Taiwan","repo":"https://packagist.tw/"},{"name":"Europe, Finland","repo":"https://packagist.fi"},{"name":"Europe, Germany","repo":"https://packagist.hesse.im"},{"name":"South America, Brazil","repo":"https://packagist.com.br"}]', true);

        if (! $packagistMirrors) {
            $fresnsItems = Config::where('item_key', 'fresns_items')->first();

            if ($fresnsItems) {
                $fresnsItems->item_key = 'packagist_mirrors';
                $fresnsItems->item_value = $itemValue;
                $fresnsItems->item_type = 'array';
                $fresnsItems->item_tag = 'systems';
                $fresnsItems->is_multilingual = 0;
                $fresnsItems->is_custom = 0;
                $fresnsItems->is_api = 0;
                $fresnsItems->save();
            } else {
                $newConfig = new Config;
                $newConfig->item_key = 'packagist_mirrors';
                $newConfig->item_value = $itemValue;
                $newConfig->item_type = 'array';
                $newConfig->item_tag = 'systems';
                $newConfig->is_multilingual = 0;
                $newConfig->is_custom = 0;
                $newConfig->is_api = 0;
                $newConfig->save();
            }
        }

        logger('-- -- upgrade to 2 (fresns v2.0.0-beta.2) done');

        return true;
    }

    // fresns v2.0.0-beta.3
    public static function upgradeTo3(): bool
    {
        // modify cookie prefix
        $cookiePrefix = Config::where('item_key', 'engine_cookie_prefix')->first();

        if (! $cookiePrefix) {
            $engineService = Config::where('item_key', 'engine_service')->first();

            if ($engineService) {
                $engineService->item_key = 'engine_cookie_prefix';
                $engineService->item_value = 'fresns_';
                $engineService->item_type = 'string';
                $engineService->item_tag = 'websites';
                $engineService->is_multilingual = 0;
                $engineService->is_custom = 0;
                $engineService->is_api = 1;
                $engineService->save();
            } else {
                $newConfig = new Config;
                $newConfig->item_key = 'engine_cookie_prefix';
                $newConfig->item_value = 'fresns_';
                $newConfig->item_type = 'string';
                $newConfig->item_tag = 'websites';
                $newConfig->is_multilingual = 0;
                $newConfig->is_custom = 0;
                $newConfig->is_api = 1;
                $newConfig->save();
            }
        }

        // modify account cookies status
        $accountCookieStatus = Config::where('item_key', 'account_cookie_status')->first();
        if ($accountCookieStatus) {
            $accountCookieStatus->item_key = 'account_cookies_status';
            $accountCookieStatus->save();
        }

        // modify account cookies policies
        $accountCookie = Config::where('item_key', 'account_cookie')->first();
        if ($accountCookie) {
            $accountCookie->item_key = 'account_cookies';
            $accountCookie->save();

            $langContent = Language::where('table_name', 'configs')->where('table_column', 'item_value')->where('table_key', 'account_cookie')->get();
            foreach ($langContent as $lang) {
                $lang->table_key = 'account_cookies';
                $lang->save();
            }
        }

        // modify lang pack key
        $languagePack = Config::where('item_key', 'language_pack')->first();
        if ($languagePack) {
            $packData = $languagePack->item_value;

            $newPackData = ArrUtility::editValue($packData, 'name', 'accountPoliciesCookie', 'accountPoliciesCookies');
            $newPackData = ArrUtility::editValue($newPackData, 'name', 'accountRestore', 'accountRecallDelete');

            $addPackKeys = [
                [
                    'name' => 'executionDate',
                    'canDelete' => false,
                ],
                [
                    'name' => 'accountApplyDelete',
                    'canDelete' => false,
                ],
                [
                    'name' => 'accountWaitDelete',
                    'canDelete' => false,
                ],
            ];

            $newData = array_merge($newPackData, $addPackKeys);

            $languagePack->item_value = $newData;
            $languagePack->save();
        }

        // modify lang key
        $langPackContents = Language::where('table_name', 'configs')->where('table_column', 'item_value')->where('table_key', 'language_pack_contents')->get();
        foreach ($langPackContents as $packContent) {
            $content = (object) json_decode($packContent->lang_content, true);

            $newContent = ArrUtility::editKey($content, 'accountPoliciesCookie', 'accountPoliciesCookies');
            $newContent = ArrUtility::editKey($newContent, 'accountRestore', 'accountRecallDelete');

            $langAddContent = match ($packContent->lang_tag) {
                'en' => [
                    'executionDate' => 'Execution Date',
                    'accountApplyDelete' => 'Apply Delete Account',
                    'accountWaitDelete' => 'Delete account wait execution',
                    'accountRecallDelete' => 'Recall Delete Account',
                ],
                'zh-Hans' => [
                    'executionDate' => '执行日期',
                    'accountDelete' => '注销账号',
                    'accountApplyDelete' => '申请注销',
                    'accountWaitDelete' => '账号注销等待执行中',
                    'accountRecallDelete' => '撤销注销',
                ],
                'zh-Hant' => [
                    'executionDate' => '執行日期',
                    'accountDelete' => '註銷賬號',
                    'accountApplyDelete' => '申請註銷',
                    'accountWaitDelete' => '賬號註銷等待執行中',
                    'accountRecallDelete' => '撤銷註銷',
                ],
            };

            $langNewContent = (object) array_merge((array) $newContent, (array) $langAddContent);

            $packContent->lang_content = json_encode($langNewContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
            $packContent->save();
        }

        logger('-- -- upgrade to 3 (fresns v2.0.0-beta.3) done');

        return true;
    }

    // fresns v2.0.0-beta.4
    public static function upgradeTo4(): bool
    {
        // add config key
        $cacheDatetime = Config::where('item_key', 'cache_datetime')->first();
        if (! $cacheDatetime) {
            $newConfig = new Config;
            $newConfig->item_key = 'cache_datetime';
            $newConfig->item_value = null;
            $newConfig->item_type = 'string';
            $newConfig->item_tag = 'systems';
            $newConfig->is_multilingual = 0;
            $newConfig->is_custom = 0;
            $newConfig->is_api = 1;
            $newConfig->save();
        }

        // modify config tag
        $configs = Config::where('item_tag', 'interactives')->get();
        foreach ($configs as $config) {
            $config->item_tag = 'interactions';
            $config->save();
        }

        // modify lang pack key
        $languagePack = Config::where('item_key', 'language_pack')->first();
        if ($languagePack) {
            $packData = $languagePack->item_value;

            $newPackData = ArrUtility::editValue($packData, 'name', 'notificationEmpty', 'listEmpty');

            $languagePack->item_value = $newPackData;
            $languagePack->save();
        }

        // modify lang key
        $langPackContents = Language::where('table_name', 'configs')->where('table_column', 'item_value')->where('table_key', 'language_pack_contents')->get();
        foreach ($langPackContents as $packContent) {
            $content = (object) json_decode($packContent->lang_content, true);

            $newContent = ArrUtility::editKey($content, 'notificationEmpty', 'listEmpty');

            $langAddContent = match ($packContent->lang_tag) {
                'en' => [
                    'listEmpty' => 'The list is empty, no content at the moment.',
                ],
                'zh-Hans' => [
                    'listEmpty' => '列表为空，暂无内容',
                ],
                'zh-Hant' => [
                    'listEmpty' => '列表為空，暫無內容',
                ],
            };

            $langNewContent = (object) array_merge((array) $newContent, (array) $langAddContent);

            $packContent->lang_content = json_encode($langNewContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
            $packContent->save();
        }

        logger('-- -- upgrade to 4 (fresns v2.0.0-beta.4) done');

        return true;
    }

    // fresns v2.0.0-beta.5
    public static function upgradeTo5(): bool
    {
        // modify lang pack key
        $languagePack = Config::where('item_key', 'language_pack')->first();
        if ($languagePack) {
            $packData = $languagePack->item_value;

            $addPackKeys = [
                [
                    'name' => 'home',
                    'canDelete' => false,
                ],
                [
                    'name' => 'accountPolicies',
                    'canDelete' => false,
                ],
                [
                    'name' => 'userFollowing',
                    'canDelete' => false,
                ],
                [
                    'name' => 'userUnfollow',
                    'canDelete' => false,
                ],
                [
                    'name' => 'contentActive',
                    'canDelete' => false,
                ],
            ];

            $newData = array_merge($packData, $addPackKeys);

            $languagePack->item_value = $newData;
            $languagePack->save();
        }

        // modify lang key
        $langPackContents = Language::where('table_name', 'configs')->where('table_column', 'item_value')->where('table_key', 'language_pack_contents')->get();
        foreach ($langPackContents as $packContent) {
            $content = (object) json_decode($packContent->lang_content, true);

            $langAddContent = match ($packContent->lang_tag) {
                'en' => [
                    'home' => 'Home',
                    'accountPolicies' => 'Privacy & Terms',
                    'userFollowing' => 'Following',
                    'userUnfollow' => 'Unfollow',
                    'contentActive' => 'Active',
                ],
                'zh-Hans' => [
                    'home' => '首页',
                    'accountPolicies' => '隐私权和条款',
                    'userFollowing' => '正在关注',
                    'userUnfollow' => '取消关注',
                    'contentActive' => '活跃',
                ],
                'zh-Hant' => [
                    'home' => '首頁',
                    'accountPolicies' => '私隱權和條款',
                    'userFollowing' => '正在跟隨',
                    'userUnfollow' => '取消跟隨',
                    'contentActive' => '活躍',
                ],
            };

            $langNewContent = (object) array_merge((array) $content, (array) $langAddContent);

            $packContent->lang_content = json_encode($langNewContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
            $packContent->save();
        }

        logger('-- -- upgrade to 5 (fresns v2.0.0-beta.5) done');

        return true;
    }

    // fresns v2.0.0-beta.6
    public static function upgradeTo6(): bool
    {
        // modify lang pack key
        $languagePack = Config::where('item_key', 'language_pack')->first();
        if ($languagePack) {
            $packData = $languagePack->item_value;

            $addPackKeys = [
                [
                    'name' => 'automatic',
                    'canDelete' => false,
                ],
                [
                    'name' => 'discover',
                    'canDelete' => false,
                ],
                [
                    'name' => 'darkMode',
                    'canDelete' => false,
                ],
                [
                    'name' => 'admin',
                    'canDelete' => false,
                ],
                [
                    'name' => 'groupAdmin',
                    'canDelete' => false,
                ],
                [
                    'name' => 'userMy',
                    'canDelete' => false,
                ],
                [
                    'name' => 'userMe',
                    'canDelete' => false,
                ],
                [
                    'name' => 'contentLatestCommentTime',
                    'canDelete' => false,
                ],
            ];

            $newData = array_merge($packData, $addPackKeys);

            $languagePack->item_value = $newData;
            $languagePack->save();
        }

        // modify lang key
        $langPackContents = Language::where('table_name', 'configs')->where('table_column', 'item_value')->where('table_key', 'language_pack_contents')->get();
        foreach ($langPackContents as $packContent) {
            $content = (object) json_decode($packContent->lang_content, true);

            $langAddContent = match ($packContent->lang_tag) {
                'en' => [
                    'automatic' => 'Automatic',
                    'discover' => 'Discover',
                    'darkMode' => 'Dark Mode',
                    'admin' => 'Administrator',
                    'groupAdmin' => 'Administrator',
                    'userMy' => 'My',
                    'userMe' => 'Me',
                    'contentLatestCommentTime' => 'Latest Comment Time',
                ],
                'zh-Hans' => [
                    'automatic' => '自动',
                    'discover' => '发现',
                    'darkMode' => '深色模式',
                    'admin' => '管理员',
                    'groupAdmin' => '小组管理员',
                    'userMy' => '我的',
                    'userMe' => '我',
                    'contentLatestCommentTime' => '最新评论时间',
                ],
                'zh-Hant' => [
                    'automatic' => '自動',
                    'discover' => '發現',
                    'darkMode' => '深色模式',
                    'admin' => '管理員',
                    'groupAdmin' => '社團管理員',
                    'userMy' => '我的',
                    'userMe' => '我',
                    'contentLatestCommentTime' => '最新留言時間',
                ],
            };

            $langNewContent = (object) array_merge((array) $content, (array) $langAddContent);

            $packContent->lang_content = json_encode($langNewContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
            $packContent->save();
        }

        logger('-- -- upgrade to 6 (fresns v2.0.0-beta.6) done');

        return true;
    }

    // fresns v2.0.0-beta.7
    public static function upgradeTo7(): bool
    {
        // modify config key
        $langArr = Language::where('table_name', 'configs')->where('table_key', 'account_cookie')->get();
        foreach ($langArr as $lang) {
            $lang->update([
                'table_key' => 'account_cookies',
            ]);
        }

        // modify lang pack key
        $languagePack = Config::where('item_key', 'language_pack')->first();
        if ($languagePack) {
            $value = '[{"name":"language","canDelete":false},{"name":"errorUnknown","canDelete":false},{"name":"errorEmpty","canDelete":false},{"name":"errorNotExist","canDelete":false},{"name":"errorNotMatch","canDelete":false},{"name":"errorNoInfo","canDelete":false},{"name":"errorNoLogin","canDelete":false},{"name":"errorTimeout","canDelete":false},{"name":"errorRejection","canDelete":false},{"name":"errorUnavailable","canDelete":false},{"name":"errorIp","canDelete":false},{"name":"loading","canDelete":false},{"name":"loadMore","canDelete":false},{"name":"clickToLoadMore","canDelete":false},{"name":"scrollUpToLoadMore","canDelete":false},{"name":"scrollDownToLoadMore","canDelete":false},{"name":"executionDate","canDelete":false},{"name":"success","canDelete":false},{"name":"failed","canDelete":false},{"name":"warning","canDelete":false},{"name":"danger","canDelete":false},{"name":"setting","canDelete":false},{"name":"config","canDelete":false},{"name":"reset","canDelete":false},{"name":"refresh","canDelete":false},{"name":"reload","canDelete":false},{"name":"automatic","canDelete":false},{"name":"modify","canDelete":false},{"name":"edit","canDelete":false},{"name":"delete","canDelete":false},{"name":"add","canDelete":false},{"name":"remove","canDelete":false},{"name":"previous","canDelete":false},{"name":"next","canDelete":false},{"name":"return","canDelete":false},{"name":"close","canDelete":false},{"name":"cancel","canDelete":false},{"name":"dismiss","canDelete":false},{"name":"activate","canDelete":false},{"name":"deactivate","canDelete":false},{"name":"install","canDelete":false},{"name":"uninstall","canDelete":false},{"name":"check","canDelete":false},{"name":"done","canDelete":false},{"name":"search","canDelete":false},{"name":"location","canDelete":false},{"name":"home","canDelete":false},{"name":"discover","canDelete":false},{"name":"list","canDelete":false},{"name":"choose","canDelete":false},{"name":"update","canDelete":false},{"name":"recall","canDelete":false},{"name":"submit","canDelete":false},{"name":"save","canDelete":false},{"name":"saveChanges","canDelete":false},{"name":"confirm","canDelete":false},{"name":"send","canDelete":false},{"name":"rankNum","canDelete":false},{"name":"type","canDelete":false},{"name":"view","canDelete":false},{"name":"share","canDelete":false},{"name":"more","canDelete":false},{"name":"file","canDelete":false},{"name":"image","canDelete":false},{"name":"video","canDelete":false},{"name":"audio","canDelete":false},{"name":"document","canDelete":false},{"name":"learnMore","canDelete":false},{"name":"pleaseEnter","canDelete":false},{"name":"copyLink","canDelete":false},{"name":"copySuccess","canDelete":false},{"name":"darkMode","canDelete":false},{"name":"modifierCount","canDelete":false},{"name":"modifierOr","canDelete":false},{"name":"modifierYear","canDelete":false},{"name":"modifierMonth","canDelete":false},{"name":"modifierDay","canDelete":false},{"name":"modifierDays","canDelete":false},{"name":"modifierLength","canDelete":false},{"name":"unitSecond","canDelete":false},{"name":"unitMinute","canDelete":false},{"name":"unitWithinMinute","canDelete":false},{"name":"unitCharacter","canDelete":false},{"name":"unitNumber","canDelete":false},{"name":"unitNumberOfTimes","canDelete":false},{"name":"unitWordCount","canDelete":false},{"name":"unitKm","canDelete":false},{"name":"unitMi","canDelete":false},{"name":"listEmpty","canDelete":false},{"name":"ipLocation","canDelete":false},{"name":"optionLanguage","canDelete":false},{"name":"optionUser","canDelete":false},{"name":"private","canDelete":false},{"name":"email","canDelete":false},{"name":"newEmail","canDelete":false},{"name":"phone","canDelete":false},{"name":"newPhone","canDelete":false},{"name":"countryCode","canDelete":false},{"name":"verifyCode","canDelete":false},{"name":"emailVerifyCode","canDelete":false},{"name":"smsVerifyCode","canDelete":false},{"name":"newVerifyCode","canDelete":false},{"name":"sendVerifyCode","canDelete":false},{"name":"resendVerifyCode","canDelete":false},{"name":"account","canDelete":false},{"name":"accountPassword","canDelete":false},{"name":"accountType","canDelete":false},{"name":"accountJoin","canDelete":false},{"name":"accountRegister","canDelete":false},{"name":"accountLogin","canDelete":false},{"name":"accountLoginByPassword","canDelete":false},{"name":"accountLoginByCode","canDelete":false},{"name":"accountLoginByConnects","canDelete":false},{"name":"accountLogout","canDelete":false},{"name":"accountDelete","canDelete":false},{"name":"accountApplyDelete","canDelete":false},{"name":"accountWaitDelete","canDelete":false},{"name":"accountRecallDelete","canDelete":false},{"name":"accountReset","canDelete":false},{"name":"accountError","canDelete":false},{"name":"accountInfo","canDelete":false},{"name":"accountPolicies","canDelete":false},{"name":"accountPoliciesTerms","canDelete":false},{"name":"accountPoliciesPrivacy","canDelete":false},{"name":"accountPoliciesCookies","canDelete":false},{"name":"accountPoliciesDelete","canDelete":false},{"name":"password","canDelete":false},{"name":"passwordCurrent","canDelete":false},{"name":"passwordNew","canDelete":false},{"name":"passwordAgain","canDelete":false},{"name":"passwordAgainError","canDelete":false},{"name":"passwordError","canDelete":false},{"name":"passwordForgot","canDelete":false},{"name":"passwordInfo","canDelete":false},{"name":"passwordInfoNumbers","canDelete":false},{"name":"passwordInfoLowercaseLetters","canDelete":false},{"name":"passwordInfoUppercaseLetters","canDelete":false},{"name":"passwordInfoSymbols","canDelete":false},{"name":"userCurrent","canDelete":false},{"name":"userPassword","canDelete":false},{"name":"userAuthPassword","canDelete":false},{"name":"userAuth","canDelete":false},{"name":"userMy","canDelete":false},{"name":"userMe","canDelete":false},{"name":"userProfile","canDelete":false},{"name":"userAvatar","canDelete":false},{"name":"userGender","canDelete":false},{"name":"userBirthday","canDelete":false},{"name":"userTimeZone","canDelete":false},{"name":"userLanguage","canDelete":false},{"name":"userFollowing","canDelete":false},{"name":"userUnfollow","canDelete":false},{"name":"userFollowMutual","canDelete":false},{"name":"userFollowMe","canDelete":false},{"name":"userBlockMe","canDelete":false},{"name":"userFollowersYouKnow","canDelete":false},{"name":"userFollowersYouFollow","canDelete":false},{"name":"walletStatus","canDelete":false},{"name":"walletBalance","canDelete":false},{"name":"walletFreezeAmount","canDelete":false},{"name":"walletPassword","canDelete":false},{"name":"walletLogs","canDelete":false},{"name":"walletRecharge","canDelete":false},{"name":"walletWithdraw","canDelete":false},{"name":"notificationMarkAllAsRead","canDelete":false},{"name":"notificationMarkAsRead","canDelete":false},{"name":"notificationLike","canDelete":false},{"name":"notificationDislike","canDelete":false},{"name":"notificationFollow","canDelete":false},{"name":"notificationBlock","canDelete":false},{"name":"notificationMention","canDelete":false},{"name":"notificationComment","canDelete":false},{"name":"settingGeneral","canDelete":false},{"name":"settingPreference","canDelete":false},{"name":"settingAccount","canDelete":false},{"name":"settingConnect","canDelete":false},{"name":"settingType","canDelete":false},{"name":"settingAlready","canDelete":false},{"name":"settingNot","canDelete":false},{"name":"settingWarning","canDelete":false},{"name":"settingCheckError","canDelete":false},{"name":"settingAllowAll","canDelete":false},{"name":"settingAllowMyFollow","canDelete":false},{"name":"settingAllowMyFollowAndVerified","canDelete":false},{"name":"settingAllowNotAll","canDelete":false},{"name":"settingIntervalDays","canDelete":false},{"name":"settingLastTime","canDelete":false},{"name":"settingNameWarning","canDelete":false},{"name":"settingNicknameWarning","canDelete":false},{"name":"settingGenderNull","canDelete":false},{"name":"settingGenderMale","canDelete":false},{"name":"settingGenderFemale","canDelete":false},{"name":"settingConnectAdd","canDelete":false},{"name":"settingConnectRemove","canDelete":false},{"name":"admin","canDelete":false},{"name":"groupAdmin","canDelete":false},{"name":"editorFullFunctions","canDelete":false},{"name":"editorRequired","canDelete":false},{"name":"editorOptional","canDelete":false},{"name":"editorCreate","canDelete":false},{"name":"editorNoChooseGroup","canDelete":false},{"name":"editorNoGroup","canDelete":false},{"name":"editorMyFollowGroup","canDelete":false},{"name":"editorStickers","canDelete":false},{"name":"editorImages","canDelete":false},{"name":"editorVideos","canDelete":false},{"name":"editorVideoPlay","canDelete":false},{"name":"editorAudios","canDelete":false},{"name":"editorDocuments","canDelete":false},{"name":"editorTitle","canDelete":false},{"name":"editorMention","canDelete":false},{"name":"editorHashtag","canDelete":false},{"name":"editorExtends","canDelete":false},{"name":"editorContent","canDelete":false},{"name":"editorLocation","canDelete":false},{"name":"editorAnonymous","canDelete":false},{"name":"editorUpload","canDelete":false},{"name":"editorUploadInfo","canDelete":false},{"name":"editorUploadExtensions","canDelete":false},{"name":"editorUploadMaxSize","canDelete":false},{"name":"editorUploadMaxTime","canDelete":false},{"name":"editorUploadNumber","canDelete":false},{"name":"editorUploadBtn","canDelete":false},{"name":"editorLimitTitle","canDelete":false},{"name":"editorLimitTypeName","canDelete":false},{"name":"editorLimitType1Desc","canDelete":false},{"name":"editorLimitType2Desc","canDelete":false},{"name":"editorLimitDateName","canDelete":false},{"name":"editorLimitCycleName","canDelete":false},{"name":"editorLimitRuleName","canDelete":false},{"name":"editorLimitRule1Desc","canDelete":false},{"name":"editorLimitRule2Desc","canDelete":false},{"name":"editorRoleLimitTitle","canDelete":false},{"name":"editorMainRoleTitle","canDelete":false},{"name":"editorEditTimeTitle","canDelete":false},{"name":"editorEditTimeDesc","canDelete":false},{"name":"editorAllowTitle","canDelete":false},{"name":"editorAllowRoleName","canDelete":false},{"name":"editorAllowUserName","canDelete":false},{"name":"editorAllowProportionName","canDelete":false},{"name":"editorAllowBtnName","canDelete":false},{"name":"editorCommentBtnTitle","canDelete":false},{"name":"editorCommentBtnName","canDelete":false},{"name":"editorUserListTitle","canDelete":false},{"name":"editorUserListName","canDelete":false},{"name":"contentReview","canDelete":false},{"name":"contentLoginError","canDelete":false},{"name":"contentAllList","canDelete":false},{"name":"contentNewList","canDelete":false},{"name":"contentHotList","canDelete":false},{"name":"contentBrowse","canDelete":false},{"name":"contentDigest","canDelete":false},{"name":"contentSticky","canDelete":false},{"name":"contentActive","canDelete":false},{"name":"contentRecommend","canDelete":false},{"name":"contentFull","canDelete":false},{"name":"contentViewOriginal","canDelete":false},{"name":"contentCreator","canDelete":false},{"name":"contentCreatorAnonymous","canDelete":false},{"name":"contentCreatorDeactivate","canDelete":false},{"name":"contentCreatorLiked","canDelete":false},{"name":"contentPublishTime","canDelete":false},{"name":"contentCommentTime","canDelete":false},{"name":"contentLatestCommentTime","canDelete":false},{"name":"contentPublishedOn","canDelete":false},{"name":"contentEditedOn","canDelete":false},{"name":"contentFileUploader","canDelete":false},{"name":"contentFileDownloader","canDelete":false},{"name":"contentFileDownloaderDesc","canDelete":false},{"name":"contentFileDownload","canDelete":false},{"name":"contentImageLong","canDelete":false},{"name":"contentVideoPlay","canDelete":false},{"name":"contentDocumentDetail","canDelete":false},{"name":"contentDocumentInfo","canDelete":false},{"name":"contentAllowInfo","canDelete":false},{"name":"contentTopComment","canDelete":false},{"name":"contentCommentWho","canDelete":false},{"name":"contentCommentClose","canDelete":false},{"name":"contentCommentCountDesc","canDelete":false},{"name":"contentCommentNotPublic","canDelete":false},{"name":"contentCommentVisibilityRuleTip","canDelete":false},{"name":"getLocation","canDelete":false},{"name":"reloadLocation","canDelete":false},{"name":"locationLoading","canDelete":false},{"name":"getLocationError","canDelete":false}]';

            $itemValue = json_decode($value, true);

            $languagePack->update([
                'item_value' => $itemValue,
            ]);
        }

        // modify lang key
        $langPackContents = Language::where('table_name', 'configs')->where('table_key', 'language_pack_contents')->get();
        foreach ($langPackContents as $packContent) {
            $langContent = match ($packContent->lang_tag) {
                'en' => '{"language":"Language","errorUnknown":"Unknown error","errorEmpty":"Cannot be empty","errorNotExist":"Does not exist","errorNotMatch":"You two input do not match","errorNoInfo":"Get info failed","errorNoLogin":"Unable to operate without login","errorTimeout":"Timeout","errorRejection":"Service Rejected","errorUnavailable":"Temporarily Unavailable","errorIp":"The IP location is unknown","loading":"Loading...","loadMore":"Load More","clickToLoadMore":"Click to load more","scrollUpToLoadMore":"Scroll up to load more","scrollDownToLoadMore":"Scroll down to load more","executionDate":"Execution Date","success":"Success","failed":"Failed","warning":"Warning","danger":"Danger","setting":"Setting","config":"Configuration","reset":"Reset","refresh":"Refresh","reload":"Reload","automatic":"Automatic","modify":"Modify","edit":"Edit","delete":"Delete","add":"Add","remove":"Remove","previous":"Previous","next":"Next","return":"Return","close":"Close","cancel":"Cancel","dismiss":"Dismiss","activate":"Activate","deactivate":"Deactivate","install":"Install","uninstall":"Uninstall","check":"Check","done":"Done","search":"Search","location":"Location","home":"Home","discover":"Discover","list":"List","choose":"Choose","update":"Update","recall":"Recall","submit":"Submit","save":"Save","saveChanges":"Save changes","confirm":"Confirm","send":"Send","rankNum":"Order","type":"Type","view":"View","share":"Share","more":"More","file":"File","image":"Image","video":"Video","audio":"Audio","document":"Document","learnMore":"Learn more","pleaseEnter":"Please enter","copyLink":"Copy Link","copySuccess":"Copy Success","darkMode":"Dark Mode","modifierCount":"Total","modifierOr":"or","modifierYear":"Year","modifierMonth":"Month","modifierDay":"Day","modifierDays":"Days","modifierLength":"Length","unitSecond":"Second","unitMinute":"Minute","unitWithinMinute":"Within Minute","unitCharacter":"Character","unitNumber":"Number","unitNumberOfTimes":"Number of times","unitWordCount":"Word Count","unitKm":"Kilometer","unitMi":"Mile","listEmpty":"The list is empty, no content at the moment.","ipLocation":"IP Location","optionLanguage":"Switch Language","optionUser":"Switch user","private":"Introduction to the private model","email":"E-Mail","newEmail":"New E-Mail","phone":"Phone Number","newPhone":"New Phone","countryCode":"Country Code","verifyCode":"Verify Code","emailVerifyCode":"Mail code","smsVerifyCode":"Sms Code","newVerifyCode":"New Verify Code","sendVerifyCode":"Send Code","resendVerifyCode":"Resend Code","account":"Account","accountPassword":"Account Password","accountType":"Account Type","accountJoin":"Apply for Join","accountRegister":"Sign up","accountLogin":"Sign In","accountLoginByPassword":"Password Login","accountLoginByCode":"Code Login","accountLoginByConnects":"Quick Login","accountLogout":"Logout Account","accountDelete":"Delete Account","accountApplyDelete":"Apply Delete Account","accountWaitDelete":"Delete account wait execution","accountRecallDelete":"Recall Delete Account","accountReset":"Reset Password","accountError":"Account Error","accountInfo":"By registering, you agree to the terms and conditions of this site","accountPolicies":"Privacy & Terms","accountPoliciesTerms":"Terms","accountPoliciesPrivacy":"Data Policy","accountPoliciesCookies":"Cookies Policy","accountPoliciesDelete":"Delete Account Description","password":"Password","passwordCurrent":"Current password","passwordNew":"New password","passwordAgain":"Enter the password again","passwordAgainError":"The new password entered twice does not match","passwordError":"Password error","passwordForgot":"Forgot your password","passwordInfo":"Password must contain","passwordInfoNumbers":"Numbers","passwordInfoLowercaseLetters":"Lowercase Letters","passwordInfoUppercaseLetters":"Uppercase Letters","passwordInfoSymbols":"Symbols(Except space)","userCurrent":"Current User","userPassword":"Password Login","userAuthPassword":"Auth Password","userAuth":"Enter","userMy":"My","userMe":"Me","userProfile":"Profile","userAvatar":"Avatar","userGender":"Gender","userBirthday":"Birthday","userTimeZone":"TimeZone","userLanguage":"Language","userFollowing":"Following","userUnfollow":"Unfollow","userFollowMutual":"Mutual follow","userFollowMe":"Followed you","userBlockMe":"Blocked you","userFollowersYouKnow":"and others are also following it","userFollowersYouFollow":"The person you are following is also following it","walletStatus":"Wallet Status","walletBalance":"Balance","walletFreezeAmount":"Freeze Amount","walletPassword":"Password","walletLogs":"Transaction History","walletRecharge":"Recharge","walletWithdraw":"Withdraw","notificationMarkAllAsRead":"Mark all as read","notificationMarkAsRead":"Mark as read","notificationLike":"Liked you","notificationDislike":"Disliked you","notificationFollow":"Followed you","notificationBlock":"Blocked you","notificationMention":"Mentioned you","notificationComment":"Commented you","settingGeneral":"General","settingPreference":"Preference","settingAccount":"Account","settingConnect":"Connects","settingType":"Edit Type","settingAlready":"Already set","settingNot":"Not set","settingWarning":"To protect the security of your account, please verify your identity and proceed to the next step after successful verification","settingCheckError":"Operation verification failed, please pass the verification first and then operate","settingAllowAll":"Allow all users","settingAllowMyFollow":"Only users that I am allowed to follow","settingAllowMyFollowAndVerified":"users I follow and users I have certified","settingAllowNotAll":"Do not allow all users","settingIntervalDays":"Edit interval","settingLastTime":"Last edit time","settingNameWarning":"Alphabet and numbers only, can be pure letters or mixed with numbers, but not pure numbers","settingNicknameWarning":"No punctuation or special symbols","settingGenderNull":"Confidential","settingGenderMale":"Male","settingGenderFemale":"Female","settingConnectAdd":"Associated","settingConnectRemove":"Dissolution","admin":"Administrator","groupAdmin":"Administrator","editorFullFunctions":"Go to Senior Editor","editorRequired":"Required","editorOptional":"Optional","editorCreate":"Create a new draft","editorNoChooseGroup":"Not selected","editorNoGroup":"Do not send to","editorMyFollowGroup":"I follow","editorStickers":"Stickers","editorImages":"Images","editorVideos":"Videos","editorVideoPlay":"Upload successfully and playable after publication","editorAudios":"Audios","editorDocuments":"Docs","editorTitle":"Title","editorMention":"Mention","editorHashtag":"Hashtag","editorExtends":"Extends","editorContent":"Content","editorLocation":"Location","editorAnonymous":"Anonymous","editorUpload":"Upload","editorUploadInfo":"Please select upload resources","editorUploadExtensions":"Extensions","editorUploadMaxSize":"Max size","editorUploadMaxTime":"Max time","editorUploadNumber":"Max number","editorUploadBtn":"Confirm","editorLimitTitle":"Post restriction reminder","editorLimitTypeName":"Restriction type","editorLimitType1Desc":"Specify date range restrictions","editorLimitType2Desc":"Cycle limits within the time range of each day","editorLimitDateName":"Date range","editorLimitCycleName":"Time range","editorLimitRuleName":"Restriction rules","editorLimitRule1Desc":"It can be published, but it needs to be reviewed","editorLimitRule2Desc":"Prohibited to publish","editorLimitPromptName":"Restrictions","editorRoleLimitTitle":"Publishing restriction information of role permissions","editorMainRoleTitle":"account master role","editorEditTimeTitle":"After the content is published successfully, it can only be edited within the specified time. After the time-out, it can not be edited again, but it can be deleted.","editorEditTimeDesc":"Remaining Time","editorAllowTitle":"Permissions information","editorAllowRoleName":"Specify account role to be accessible","editorAllowUserName":"Specified accounts can access","editorAllowProportionName":"Proportion of content before trial reading","editorAllowBtnName":"Get read permission button text","editorCommentBtnTitle":"Comment Button Information","editorCommentBtnName":"Button Name","editorUserListTitle":"Affiliate Member Configuration","editorUserListName":"Affiliate Member Name","contentReview":"Content review in progress","contentLoginError":"Login is required to view","contentAllList":"All","contentNewList":"New","contentHotList":"Hot","contentBrowse":"Browse","contentDigest":"Digest","contentSticky":"Sticky","contentActive":"Active","contentRecommend":"Recommend","contentFull":"Full","contentViewOriginal":"View Original Article","contentCreator":"Creator","contentCreatorAnonymous":"Anonymous","contentCreatorDeactivate":"Account Deactivate","contentCreatorLiked":"The author liked the comment","contentPublishTime":"Publish Time","contentCommentTime":"Comment Time","contentLatestCommentTime":"Latest Comment Time","contentPublishedOn":"Published on","contentEditedOn":"Edited on","contentFileUploader":"Uploader","contentFileDownloader":"Downloader","contentFileDownloaderDesc":"Only 30 user avatars are displayed","contentFileDownload":"Download","contentImageLong":"Long","contentVideoPlay":"Play","contentDocumentDetail":"Detail","contentDocumentInfo":"This document is protected by Fresnshare and may only be downloaded and viewed by users of this site, all downloads are recorded and should not be distributed.","contentAllowInfo":"Trial content available","contentTopComment":"Top Comment","contentCommentWho":"Who can comment on the post?","contentCommentClose":"This post is closed to comments","contentCommentCountDesc":"comments","contentCommentNotPublic":"Comments are only visible to the creator of the post","contentCommentVisibilityRuleTip":"Comments are beyond the visible period, No longer displayed.","getLocation":"Get Location","reloadLocation":"Reload Location","locationLoading":"Location Loading...","getLocationError":"Your device does not support location or refuses authorization"}',
                'zh-Hans' => '{"language":"语言","errorUnknown":"未知错误","errorEmpty":"不能为空","errorNotExist":"不存在","errorNotMatch":"两次输入不一致","errorNoInfo":"无法获得信息","errorNoLogin":"未登录无法操作","errorTimeout":"服务超时","errorRejection":"服务被拒绝","errorUnavailable":"暂时无法使用","errorIp":"IP 属地未知","loading":"数据加载中...","loadMore":"加载更多","clickToLoadMore":"点击加载更多","scrollUpToLoadMore":"上滑加载更多","scrollDownToLoadMore":"下滑加载更多","executionDate":"执行日期","success":"成功","failed":"失败","warning":"警告","danger":"危险","setting":"设置","config":"配置","reset":"重置","refresh":"刷新","reload":"重新载入","automatic":"自动","modify":"修改","edit":"编辑","delete":"删除","add":"新增","remove":"移除","previous":"上一步","next":"下一步","return":"返回","close":"关闭","cancel":"取消","dismiss":"驳回","activate":"启用","deactivate":"停用","install":"安装","uninstall":"卸载","check":"验证","done":"完成","search":"搜索","location":"位置","home":"首页","discover":"发现","list":"列表","choose":"选择","update":"更新","recall":"撤回","submit":"提交","save":"保存","saveChanges":"保存更改","confirm":"确认","send":"发送","rankNum":"排序","type":"类型","view":"查看","share":"分享","more":"更多","file":"文件","image":"图片","video":"视频","audio":"音频","document":"文档","learnMore":"了解详情","pleaseEnter":"请输入","copyLink":"复制链接","copySuccess":"复制成功","darkMode":"深色模式","modifierCount":"共","modifierOr":"或","modifierYear":"年","modifierMonth":"月","modifierDay":"日","modifierDays":"天","modifierLength":"长度","unitSecond":"秒","unitMinute":"分钟","unitWithinMinute":"分钟以内","unitCharacter":"字符","unitNumber":"个数","unitNumberOfTimes":"次数","unitWordCount":"字数","unitKm":"公里","unitMi":"英里","listEmpty":"列表为空，暂无内容","ipLocation":"IP 属地","optionLanguage":"切换语言","optionUser":"切换用户","private":"私有模式介绍","email":"邮箱","newEmail":"新邮箱","phone":"手机号","newPhone":"新手机号","countryCode":"国际区号","verifyCode":"验证码","emailVerifyCode":"邮件验证码","smsVerifyCode":"短信验证码","newVerifyCode":"新验证码","sendVerifyCode":"获取验证码","resendVerifyCode":"重新发送","account":"账号","accountPassword":"账号密码","accountType":"账号类型","accountJoin":"申请加入","accountRegister":"注册","accountLogin":"登录","accountLoginByPassword":"密码登录","accountLoginByCode":"验证码登录","accountLoginByConnects":"快速登录","accountLogout":"退出登录","accountDelete":"注销账号","accountApplyDelete":"申请注销","accountWaitDelete":"账号注销等待执行中","accountRecallDelete":"撤销注销","accountReset":"重置密码","accountError":"账号错误","accountInfo":"注册即表示同意本站条款","accountPolicies":"隐私权和条款","accountPoliciesTerms":"服务条款","accountPoliciesPrivacy":"隐私政策","accountPoliciesCookies":"Cookies 使用条款","accountPoliciesDelete":"注销说明","password":"密码","passwordCurrent":"当前密码","passwordNew":"新密码","passwordAgain":"再输一次密码","passwordAgainError":"两次输入的新密码不一致","passwordError":"密码错误","passwordForgot":"忘记密码","passwordInfo":"密码必须包含","passwordInfoNumbers":"数字","passwordInfoLowercaseLetters":"小写字母","passwordInfoUppercaseLetters":"大写字母","passwordInfoSymbols":"字符(除空格)","userCurrent":"当前用户","userPassword":"密码登录","userAuthPassword":"用户密码","userAuth":"进入社区","userMy":"我的","userMe":"我","userProfile":"个人信息","userAvatar":"头像","userGender":"性别","userBirthday":"生日","userTimeZone":"时区","userLanguage":"语言","userFollowing":"正在关注","userUnfollow":"取消关注","userFollowMutual":"互相关注","userFollowMe":"关注了你","userBlockMe":"拉黑了你","userFollowersYouKnow":"等人也关注了 TA","userFollowersYouFollow":"你关注的人也在关注他","walletStatus":"钱包状态","walletBalance":"余额","walletFreezeAmount":"不可用金额","walletPassword":"钱包密码","walletLogs":"钱包交易记录","walletRecharge":"充值","walletWithdraw":"提现","notificationMarkAllAsRead":"标记全部为已读","notificationMarkAsRead":"标记为已读","notificationLike":"点赞了你","notificationDislike":"踩了你","notificationFollow":"关注了你","notificationBlock":"屏蔽了你","notificationMention":"提及了你","notificationComment":"评论了你","settingGeneral":"个人资料","settingPreference":"偏好设置","settingAccount":"账号设置","settingConnect":"互联信息","settingType":"修改方式","settingAlready":"已设置","settingNot":"未设置","settingWarning":"为了保护你的帐号安全，请验证身份，验证成功后进行下一步操作","settingCheckError":"操作验证失败，请先通过验证再操作","settingAllowAll":"允许所有用户","settingAllowMyFollow":"仅允许我关注的用户","settingAllowMyFollowAndVerified":"我关注的用户和已认证的用户","settingAllowNotAll":"不允许所有用户","settingIntervalDays":"修改间隔天数","settingLastTime":"上次修改时间","settingNameWarning":"仅支持英文字母和数字，可以纯字母或者与数字混合，但不能纯数字","settingNicknameWarning":"不能带标点符号或特殊符号","settingGenderNull":"保密","settingGenderMale":"男","settingGenderFemale":"女","settingConnectAdd":"绑定","settingConnectRemove":"解绑","admin":"管理员","groupAdmin":"小组管理员","editorFullFunctions":"进入高级编辑模式","editorRequired":"必填","editorOptional":"非必填","editorCreate":"创建新草稿","editorNoChooseGroup":"未选择","editorNoGroup":"不发到","editorMyFollowGroup":"我关注的","editorStickers":"表情","editorImages":"图片","editorVideos":"视频","editorVideoPlay":"上传成功，发表后可播放","editorAudios":"音频","editorDocuments":"文档","editorTitle":"标题","editorMention":"艾特","editorHashtag":"话题","editorExtends":"扩展","editorContent":"正文","editorLocation":"添加位置","editorAnonymous":"是否匿名","editorUpload":"上传","editorUploadInfo":"请选择上传资源","editorUploadExtensions":"支持的扩展名","editorUploadMaxSize":"支持的最大尺寸","editorUploadMaxTime":"支持的最长时间","editorUploadNumber":"支持上传的数量","editorUploadBtn":"确认上传","editorLimitTitle":"发表限制提醒","editorLimitTypeName":"限制类型","editorLimitType1Desc":"指定日期范围内限制","editorLimitType2Desc":"每天的时间段范围内循环限制","editorLimitDateName":"日期范围","editorLimitCycleName":"时间范围","editorLimitRuleName":"限制规则","editorLimitRule1Desc":"可以发表，但是需要审核","editorLimitRule2Desc":"禁止发表","editorLimitPromptName":"限制说明","editorRoleLimitTitle":"角色权限发表限制信息","editorMainRoleTitle":"用户主角色","editorEditTimeTitle":"内容发表成功后，仅在规定时间内可以编辑，超时后不可再编辑，但可以删除。","editorEditTimeDesc":"剩余时间","editorAllowTitle":"权限信息","editorAllowRoleName":"指定用户角色可访问","editorAllowUserName":"指定用户可访问","editorAllowProportionName":"可试读前内容比例","editorAllowBtnName":"获取阅读权限按钮文字","editorCommentBtnTitle":"评论按钮信息","editorCommentBtnName":"按钮名称","editorUserListTitle":"特定成员配置","editorUserListName":"特定成员名称","contentReview":"内容审核中","contentLoginError":"需要登录后才能查看","contentAllList":"所有","contentNewList":"最新","contentHotList":"热门","contentBrowse":"浏览","contentDigest":"精华","contentSticky":"置顶","contentActive":"活跃","contentRecommend":"推荐","contentFull":"全文","contentViewOriginal":"查看原文","contentCreator":"作者","contentCreatorAnonymous":"匿名者","contentCreatorDeactivate":"账号已注销","contentCreatorLiked":"作者点赞了该评论","contentPublishTime":"发布时间","contentCommentTime":"评论时间","contentLatestCommentTime":"最新评论时间","contentPublishedOn":"发表于","contentEditedOn":"编辑于","contentFileUploader":"上传者","contentFileDownloader":"用户下载记录","contentFileDownloaderDesc":"仅展示 30 名用户头像","contentFileDownload":"下载","contentImageLong":"长图","contentVideoPlay":"播放","contentDocumentDetail":"文件详情","contentDocumentInfo":"本文档受 Fresns 分享保护，仅限于本站用户下载查阅，所有下载均记录在案，请勿扩散。","contentAllowInfo":"可试读内容","contentTopComment":"热评","contentCommentWho":"谁可以评论该帖子？","contentCommentClose":"该帖子已关闭评论","contentCommentCountDesc":"条回复","contentCommentNotPublic":"评论仅帖子作者可见","contentCommentVisibilityRuleTip":"评论已超出可见期限，不再显示","getLocation":"获取定位","reloadLocation":"重新定位","locationLoading":"正在定位...","getLocationError":"您的设备不支持定位或者拒绝授权"}',
                'zh-Hant' => '{"language":"語言","errorUnknown":"未知錯誤","errorEmpty":"不能為空","errorNotExist":"不存在","errorNotMatch":"兩次輸入不一致","errorNoInfo":"无法获得信息","errorNoLogin":"未登錄無法操作","errorTimeout":"服務超時","errorRejection":"服務被拒絕","errorUnavailable":"暫時無法使用","errorIp":"IP 屬地未知","loading":"數據加載中...","loadMore":"加載更多","clickToLoadMore":"點擊加載更多","scrollUpToLoadMore":"上滑加載更多","scrollDownToLoadMore":"下滑加載更多","executionDate":"執行日期","success":"成功","failed":"失敗","warning":"警告","danger":"危險","setting":"設定","config":"配置","reset":"重置","refresh":"刷新","reload":"重新載入","automatic":"自動","modify":"修改","edit":"編輯","delete":"刪除","add":"新增","remove":"移除","previous":"上一步","next":"下一步","return":"返回","close":"關閉","cancel":"取消","dismiss":"駁回","activate":"啟用","deactivate":"停用","install":"安裝","uninstall":"卸載","check":"驗證","done":"完成","search":"搜索","location":"位置","home":"首頁","discover":"發現","list":"列表","choose":"選擇","update":"更新","recall":"撤回","submit":"提交","save":"儲存","saveChanges":"儲存變更","confirm":"確認","send":"發送","rankNum":"排序","type":"類型","view":"查看","share":"分享","more":"更多","file":"文件","image":"圖片","video":"視頻","audio":"音頻","document":"文檔","learnMore":"瞭解詳情","pleaseEnter":"請輸入","copyLink":"複製鏈接","copySuccess":"複製成功","darkMode":"深色模式","modifierCount":"共","modifierOr":"或","modifierYear":"年","modifierMonth":"月","modifierDay":"日","modifierDays":"天","modifierLength":"長度","unitSecond":"秒","unitMinute":"分鐘","unitWithinMinute":"分鐘以內","unitCharacter":"字符","unitNumber":"個數","unitNumberOfTimes":"次數","unitWordCount":"字數","unitKm":"公里","unitMi":"英里","listEmpty":"列表為空，暫無內容","ipLocation":"IP 屬地","optionLanguage":"切換語言","optionUser":"切換用戶","private":"私有模式介紹","email":"郵箱","newEmail":"新郵箱","phone":"手機號","newPhone":"新手機號","countryCode":"國際區號","verifyCode":"驗證碼","emailVerifyCode":"郵件驗證碼","smsVerifyCode":"短信驗證碼","newVerifyCode":"新驗證碼","sendVerifyCode":"獲取驗證碼","resendVerifyCode":"重新發送","account":"賬號","accountPassword":"賬號密碼","accountType":"賬號類型","accountJoin":"申請加入","accountRegister":"註冊","accountLogin":"登錄","accountLoginByPassword":"密碼登錄","accountLoginByCode":"驗證碼登錄","accountLoginByConnects":"快速登錄","accountLogout":"登出賬號","accountDelete":"註銷賬號","accountApplyDelete":"申請註銷","accountWaitDelete":"賬號註銷等待執行中","accountRecallDelete":"撤銷註銷","accountReset":"重置密碼","accountError":"賬號錯誤","accountInfo":"註冊即表示同意本站條款","accountPolicies":"私隱權和條款","accountPoliciesTerms":"服務條款","accountPoliciesPrivacy":"隱私政策","accountPoliciesCookies":"Cookie 使用條款","accountPoliciesDelete":"註銷說明","password":"密碼","passwordCurrent":"當前密碼","passwordNew":"新密碼","passwordAgain":"再輸一次密碼","passwordAgainError":"兩次輸入的新密碼不一致","passwordError":"密碼錯誤","passwordForgot":"忘記密碼","passwordInfo":"密碼必須包含","passwordInfoNumbers":"數字","passwordInfoLowercaseLetters":"小寫字母","passwordInfoUppercaseLetters":"大寫字母","passwordInfoSymbols":"字符(除空格)","userCurrent":"當前用戶","userPassword":"密碼登錄","userAuthPassword":"用戶密碼","userAuth":"進入社區","userMy":"我的","userMe":"我","userProfile":"個人信息","userAvatar":"頭像","userGender":"性別","userBirthday":"生日","userTimeZone":"時區","userLanguage":"語言","userFollowing":"正在跟隨","userUnfollow":"取消跟隨","userFollowMutual":"互相跟隨","userFollowMe":"跟隨了你","userBlockMe":"封鎖了你","userFollowersYouKnow":"等人也跟隨了他","userFollowersYouFollow":"你跟隨的人也在跟隨他","walletStatus":"錢包狀態","walletBalance":"餘額","walletFreezeAmount":"不可用金額","walletPassword":"錢包密碼","walletLogs":"交易記錄","walletRecharge":"充值","walletWithdraw":"提現","notificationMarkAllAsRead":"標記全部為已讀","notificationMarkAsRead":"標記為已讀","notificationLike":"喜歡了你","notificationDislike":"不喜歡了你","notificationFollow":"跟隨了你","notificationBlock":"封鎖了你","notificationMention":"提及了你","notificationComment":"留言了你","settingGeneral":"個人資料","settingPreference":"偏好設定","settingAccount":"賬號設定","settingConnect":"互聯信息","settingType":"修改方式","settingAlready":"已設定","settingNot":"未設定","settingWarning":"為了保護你的帳號安全，請驗證身份，驗證成功後進行下一步操作","settingCheckError":"操作驗證失敗，請先通過驗證再操作","settingAllowAll":"允許所有用戶","settingAllowMyFollow":"僅允許我跟隨的用戶","settingAllowMyFollowAndVerified":"我跟隨的用戶和已認證的用戶","settingAllowNotAll":"不允許所有用戶","settingIntervalDays":"修改間隔天數","settingLastTime":"上次修改時間","settingNameWarning":"僅支持英文字母和數字，可以純字母或者與數字混合，但不能純數字","settingNicknameWarning":"不能帶標點符號或特殊符號","settingGenderNull":"保密","settingGenderMale":"男","settingGenderFemale":"女","settingConnectAdd":"關聯","settingConnectRemove":"解除","admin":"管理員","groupAdmin":"社團管理員","editorFullFunctions":"進入高級編輯模式","editorRequired":"必填","editorOptional":"非必填","editorCreate":"創建新草稿","editorNoChooseGroup":"未選擇","editorNoGroup":"不發到","editorMyFollowGroup":"我跟隨的","editorStickers":"表情","editorImages":"圖片","editorVideos":"視頻","editorVideoPlay":"上傳成功，發表後可播放","editorAudios":"音頻","editorDocuments":"文檔","editorTitle":"標題","editorMention":"艾特","editorHashtag":"話題","editorExtends":"擴展","editorContent":"正文","editorLocation":"添加位置","editorAnonymous":"是否匿名","editorUpload":"上傳","editorUploadInfo":"請選擇上傳資源","editorUploadExtensions":"支持的擴展名","editorUploadMaxSize":"支持的最大尺寸","editorUploadMaxTime":"支持的最长时间","editorUploadNumber":"支持上傳的數量","editorUploadBtn":"確認上傳","editorLimitTitle":"發表限制提醒","editorLimitTypeName":"限制類型","editorLimitType1Desc":"指定日期範圍內限制","editorLimitType2Desc":"每天的時間段範圍內循環限制","editorLimitDateName":"日期範圍","editorLimitCycleName":"時間範圍","editorLimitRuleName":"限制規則","editorLimitRule1Desc":"可以發表，但是需要審核","editorLimitRule2Desc":"禁止發表","editorLimitPromptName":"限制說明","editorRoleLimitTitle":"角色權限發表限制信息","editorMainRoleTitle":"用戶主角色","editorEditTimeTitle":"內容髮表成功後，僅在規定時間內可以編輯，超時後不可再編輯，但可以刪除。","editorEditTimeDesc":"剩餘時間","editorAllowTitle":"權限信息","editorAllowRoleName":"指定用戶角色可訪問","editorAllowUserName":"指定用戶可訪問","editorAllowProportionName":"可試讀前內容比例","editorAllowBtnName":"獲取閱讀權限按鈕文字","editorCommentBtnTitle":"留言按鈕信息","editorCommentBtnName":"按鈕名稱","editorUserListTitle":"特定成員配置","editorUserListName":"特定成員名稱","contentReview":"內容審查中","contentLoginError":"需要登錄後才能查看","contentAllList":"所有","contentNewList":"最新","contentHotList":"熱門","contentBrowse":"瀏覽","contentDigest":"精華","contentSticky":"置頂","contentActive":"活躍","contentRecommend":"推薦","contentFull":"全文","contentViewOriginal":"查看原文","contentCreator":"作者","contentCreatorAnonymous":"匿名者","contentCreatorDeactivate":"賬號已註銷","contentCreatorLiked":"作者點讚了該留言","contentPublishTime":"發表時間","contentCommentTime":"留言時間","contentLatestCommentTime":"最新留言時間","contentPublishedOn":"發表於","contentEditedOn":"編輯於","contentFileUploader":"上傳者","contentFileDownloader":"用戶下載記錄","contentFileDownloaderDesc":"僅展示 30 名用戶頭像","contentFileDownload":"下載","contentImageLong":"長圖","contentVideoPlay":"播放","contentDocumentDetail":"文件詳情","contentDocumentInfo":"本文檔受 Fresns 分享保護，僅限於本站用戶下載查閱，所有下載均記錄在案，請勿擴散。","contentAllowInfo":"可試讀內容","contentTopComment":"熱評","contentCommentWho":"誰可以留言該貼文？","contentCommentClose":"該貼文已關閉留言","contentCommentCountDesc":"條留言","contentCommentNotPublic":"留言僅貼文作者可見","contentCommentVisibilityRuleTip":"留言已超出可見期限，不再顯示","getLocation":"獲取定位","reloadLocation":"重新定位","locationLoading":"正在定位...","getLocationError":"您的設備不支持定位或者拒絕授權"}',
            };

            $packContent->update([
                'lang_content' => $langContent,
            ]);
        }

        logger('-- -- upgrade to 7 (fresns v2.0.0-beta.7) done');

        return true;
    }

    // fresns v2.0.0-beta.8
    public static function upgradeTo8(): bool
    {
        // modify
        $topCommentRequire = Config::where('item_key', 'top_comment_require')->first();
        if ($topCommentRequire) {
            $topCommentRequire->update([
                'item_key' => 'preview_post_comment_require',
                'item_value' => 10,
                'is_api' => 0,
            ]);
        }
        $commentPreview = Config::where('item_key', 'comment_preview')->first();
        if ($commentPreview) {
            $commentPreview->update([
                'item_key' => 'preview_sub_comments',
                'is_api' => 0,
            ]);
        }

        // add new
        $previewPostLikeUsers = Config::where('item_key', 'preview_post_like_users')->first();
        if (empty($previewPostLikeUsers)) {
            $newConfig = new Config;
            $newConfig->item_key = 'preview_post_like_users';
            $newConfig->item_value = '0';
            $newConfig->item_type = 'number';
            $newConfig->item_tag = 'interactions';
            $newConfig->is_multilingual = 0;
            $newConfig->is_custom = 0;
            $newConfig->is_api = 0;
            $newConfig->save();
        }

        $previewPostComments = Config::where('item_key', 'preview_post_comments')->first();
        if (empty($previewPostComments)) {
            $newConfig = new Config;
            $newConfig->item_key = 'preview_post_comments';
            $newConfig->item_value = '0';
            $newConfig->item_type = 'number';
            $newConfig->item_tag = 'interactions';
            $newConfig->is_multilingual = 0;
            $newConfig->is_custom = 0;
            $newConfig->is_api = 0;
            $newConfig->save();
        }

        $previewPostCommentSort = Config::where('item_key', 'preview_post_comment_sort')->first();
        if (empty($previewPostCommentSort)) {
            $newConfig = new Config;
            $newConfig->item_key = 'preview_post_comment_sort';
            $newConfig->item_value = 'like';
            $newConfig->item_type = 'string';
            $newConfig->item_tag = 'interactions';
            $newConfig->is_multilingual = 0;
            $newConfig->is_custom = 0;
            $newConfig->is_api = 0;
            $newConfig->save();
        }

        $previewSubCommentSort = Config::where('item_key', 'preview_sub_comment_sort')->first();
        if (empty($previewSubCommentSort)) {
            $newConfig = new Config;
            $newConfig->item_key = 'preview_sub_comment_sort';
            $newConfig->item_value = 'timeAsc';
            $newConfig->item_type = 'string';
            $newConfig->item_tag = 'interactions';
            $newConfig->is_multilingual = 0;
            $newConfig->is_custom = 0;
            $newConfig->is_api = 0;
            $newConfig->save();
        }

        // update post is_allow
        DB::table('post_appends')->update(['is_allow' => 1]);

        // code messages
        $code36113Messages = CodeMessage::where('plugin_unikey', 'Fresns')->where('code', 36113)->get();
        foreach ($code36113Messages as $code) {
            $langContent = match ($code->lang_tag) {
                'en' => 'File size exceeds the set limit',
                'zh-Hans' => '文件尺寸超出设置的限制',
                'zh-Hant' => '文件尺寸超出設置的限制',
            };

            $code->update([
                'message' => $langContent,
            ]);
        }

        $code36114Messages = CodeMessage::where('plugin_unikey', 'Fresns')->where('code', 36114)->get();
        foreach ($code36114Messages as $code) {
            $langContent = match ($code->lang_tag) {
                'en' => 'File time length exceeds the set limit',
                'zh-Hans' => '文件时长超出设置的限制',
                'zh-Hant' => '文件時長超出設置的限制',
            };

            $code->update([
                'message' => $langContent,
            ]);
        }

        $code36115Messages = CodeMessage::where('plugin_unikey', 'Fresns')->where('code', 36115)->get();
        foreach ($code36115Messages as $code) {
            $langContent = match ($code->lang_tag) {
                'en' => 'The number of files exceeds the set limit',
                'zh-Hans' => '文件数量超出设置的限制',
                'zh-Hant' => '文件數量超出設置的限制',
            };

            $code->update([
                'message' => $langContent,
            ]);
        }

        $code36116Messages = CodeMessage::where('plugin_unikey', 'Fresns')->where('code', 36116)->get();
        foreach ($code36116Messages as $code) {
            $langContent = match ($code->lang_tag) {
                'en' => 'Current role has no conversation message permission',
                'zh-Hans' => '当前角色无私信权限',
                'zh-Hant' => '當前角色無私信權限',
            };

            $code->update([
                'message' => $langContent,
            ]);
        }

        $code36117Messages = CodeMessage::where('plugin_unikey', 'Fresns')->where('code', 36117)->get();
        foreach ($code36117Messages as $code) {
            $langContent = match ($code->lang_tag) {
                'en' => 'The current role has reached the upper limit of today download, please download again tomorrow.',
                'zh-Hans' => '当前角色已经达到今天下载次数上限，请明天再下载',
                'zh-Hant' => '當前角色已經達到今天下載次數上限，請明天再下載',
            };

            $code->update([
                'message' => $langContent,
            ]);
        }

        $code36118Messages = CodeMessage::where('plugin_unikey', 'Fresns')->where('code', 36118)->where('lang_tag', 'en')->first();
        if (empty($code36118Messages)) {
            CodeMessage::updateOrCreate([
                'plugin_unikey' => 'Fresns',
                'code' => '36118',
                'lang_tag' => 'en',
            ],
            [
                'message' => 'The current number of characters has reached the maximum number and cannot be added',
            ]);
            CodeMessage::updateOrCreate([
                'plugin_unikey' => 'Fresns',
                'code' => '36118',
                'lang_tag' => 'zh-Hans',
            ],
            [
                'message' => '当前角色已经达到上限数量，无法再添加',
            ]);
            CodeMessage::updateOrCreate([
                'plugin_unikey' => 'Fresns',
                'code' => '36118',
                'lang_tag' => 'zh-Hant',
            ],
            [
                'message' => '當前角色已經達到上限數量，無法再添加',
            ]);
        }

        $code36119Messages = CodeMessage::where('plugin_unikey', 'Fresns')->where('code', 36119)->where('lang_tag', 'en')->first();
        if (empty($code36119Messages)) {
            CodeMessage::updateOrCreate([
                'plugin_unikey' => 'Fresns',
                'code' => '36119',
                'lang_tag' => 'en',
            ],
            [
                'message' => 'Publish too fast, please post again at intervals. Please check the current role settings for details',
            ]);
            CodeMessage::updateOrCreate([
                'plugin_unikey' => 'Fresns',
                'code' => '36119',
                'lang_tag' => 'zh-Hans',
            ],
            [
                'message' => '发表太快，请间隔一段时间再发。详情请查看当前角色的设置',
            ]);
            CodeMessage::updateOrCreate([
                'plugin_unikey' => 'Fresns',
                'code' => '36119',
                'lang_tag' => 'zh-Hant',
            ],
            [
                'message' => '發表太快，請間隔一段時間再發。詳情請查看當前角色的設置',
            ]);
        }

        logger('-- -- upgrade to 8 (fresns v2.0.0-beta.8) done');

        return true;
    }

    // fresns v2.0.0
    public static function upgradeTo9(): bool
    {
        // modify lang pack key
        $languagePack = Config::where('item_key', 'language_pack')->first();
        if ($languagePack) {
            $packData = $languagePack->item_value;

            $addPackKeys = [
                [
                    'name' => 'default',
                    'canDelete' => false,
                ],
                [
                    'name' => 'status',
                    'canDelete' => false,
                ],
            ];

            $newData = array_merge($packData, $addPackKeys);

            $languagePack->item_value = $newData;
            $languagePack->save();
        }

        // modify lang key
        $langPackContents = Language::where('table_name', 'configs')->where('table_column', 'item_value')->where('table_key', 'language_pack_contents')->get();
        foreach ($langPackContents as $packContent) {
            $content = (object) json_decode($packContent->lang_content, true);

            $langAddContent = match ($packContent->lang_tag) {
                'en' => [
                    'default' => 'Default',
                    'status' => 'Status',
                ],
                'zh-Hans' => [
                    'default' => '默认',
                    'status' => '状态',
                ],
                'zh-Hant' => [
                    'default' => '默認',
                    'status' => '狀態',
                ],
            };

            $langNewContent = (object) array_merge((array) $content, (array) $langAddContent);

            $packContent->lang_content = json_encode($langNewContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
            $packContent->save();
        }

        logger('-- -- upgrade to 9 (fresns v2.0.0) done');

        return true;
    }
}
