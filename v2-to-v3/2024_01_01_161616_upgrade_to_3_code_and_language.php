<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\Config;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
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
            'code_messages and language_packs',
        ]);

        // code_messages
        Schema::dropIfExists('code_messages');

        Schema::create('code_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('app_fskey', 64);
            $table->unsignedInteger('code');
            $table->json('messages')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();

            $table->unique(['app_fskey', 'code'], 'app_code');
        });

        Artisan::call('db:seed', [
            '--class' => 'CodeMessagesTableSeeder',
            '--force' => true,
        ]);

        // language_packs
        Schema::dropIfExists('language_packs');

        Schema::create('language_packs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('lang_key', 64)->unique('lang_key');
            $table->json('lang_values')->nullable();
            $table->unsignedTinyInteger('is_custom')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });

        Artisan::call('db:seed', [
            '--class' => 'LanguagePacksTableSeeder',
            '--force' => true,
        ]);

        Schema::dropIfExists('languages');

        // language_menus
        $languageMenus = Config::where('item_key', 'language_menus')->first();
        if ($languageMenus) {
            $languageMenusArr = $languageMenus->item_value;
            $updatedLanguageMenus = array_map(function ($menu) {
                if (isset($menu['rating'])) {
                    $menu['order'] = $menu['rating'];
                    unset($menu['rating']);
                }
                return $menu;
            }, $languageMenusArr);

            $languageMenus->item_value = $updatedLanguageMenus;
            $languageMenus->save();
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
