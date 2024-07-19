<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\Group;
use App\Models\Language;
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
            'groups',
        ]);

        if (Schema::hasColumn('groups', 'rating')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->renameColumn('type_mode', 'privacy');
                $table->renameColumn('type_mode_end_after', 'private_end_after');
                $table->renameColumn('type_find', 'visibility');
                $table->renameColumn('type_follow', 'follow_type');
                $table->renameColumn('rating', 'sort_order');
                $table->renameColumn('recommend_rating', 'recommend_sort_order');
            });
        }
        if (Schema::hasColumn('groups', 'name')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
        if (Schema::hasColumn('groups', 'description')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
        if (Schema::hasColumn('groups', 'sublevel_public')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->dropColumn('sublevel_public');
            });
        }
        if (Schema::hasColumn('groups', 'type')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        if (! Schema::hasColumn('groups', 'subgroup_count')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->unsignedInteger('subgroup_count')->default(0)->after('permissions');
            });
        }

        if (! Schema::hasColumn('groups', 'last_post_at')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->timestamp('last_comment_at')->nullable()->after('comment_digest_count');
                $table->timestamp('last_post_at')->nullable()->after('comment_digest_count');
            });
        }

        if (! Schema::hasColumn('hashtags', 'last_post_at')) {
            Schema::table('hashtags', function (Blueprint $table) {
                $table->timestamp('last_comment_at')->nullable()->after('comment_digest_count');
                $table->timestamp('last_post_at')->nullable()->after('comment_digest_count');
            });
        }

        if (! Schema::hasColumn('groups', 'more_info')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->json('more_info')->nullable()->after('permissions');
            });
        }

        if (! Schema::hasColumn('groups', 'name')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->unsignedSmallInteger('type')->default(1)->index('group_type')->after('user_id');
                $table->json('description')->nullable()->after('user_id');
                $table->json('name')->nullable()->after('user_id');
            });
        }

        $groups = Group::get();

        foreach ($groups as $group) {
            $languages = Language::where('table_name', 'groups')->where('table_id', $group->id)->get();

            $names = [];
            $descriptions = [];
            foreach ($languages as $lang) {
                if ($lang->table_column == 'name') {
                    $names[$lang->lang_tag] = $lang->lang_content;
                }
                if ($lang->table_column == 'description') {
                    $descriptions[$lang->lang_tag] = $lang->lang_content;
                }
            };

            $permissions = $group->permissions;
            $permissions['private_whitelist_roles'] = $group->permissions['mode_whitelist_roles'] ?? [];
            $permissions['can_publish'] = (bool) $group->parent_id;

            $group->update([
                'name' => $names,
                'description' => $descriptions,
                'permissions' => $permissions,
            ]);

            if ($group->parent_id) {
                Group::where('id', $group->parent_id)->increment('subgroup_count');
            }
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
