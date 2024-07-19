<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\Language;
use App\Models\Role;
use App\Utilities\AppUtility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
            'roles',
        ]);

        if (Schema::hasColumn('roles', 'rating')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->renameColumn('rating', 'sort_order');
            });
        }

        if (Schema::hasColumn('roles', 'name')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('name');
                $table->dropColumn('type');
            });
        }

        if (! Schema::hasColumn('roles', 'name')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->json('name')->nullable()->after('id');
                $table->string('rid', 32)->nullable()->after('id');
            });
        }

        $roles = Role::get();

        foreach ($roles as $role) {
            $rid = match ($role->id) {
                1 => 'administrator',
                2 => 'interdiction',
                3 => 'pendingreview',
                4 => 'generaluser',
                default => Str::random(8),
            };

            $languages = Language::where('table_name', 'roles')->where('table_id', $role->id)->get();

            $newName = null;
            foreach ($languages as $lang) {
                $newName[$lang->lang_tag] = $lang->lang_content;
            }

            $permissions = $role->permissions;

            $permissionsStr = json_encode($permissions);

            $newPermissions = Str::replace('post_email_verify', 'post_required_email', $permissionsStr);
            $newPermissions = Str::replace('post_phone_verify', 'post_required_phone', $newPermissions);
            $newPermissions = Str::replace('post_real_name_verify', 'post_required_kyc', $newPermissions);
            $newPermissions = Str::replace('comment_email_verify', 'comment_required_email', $newPermissions);
            $newPermissions = Str::replace('comment_phone_verify', 'comment_required_phone', $newPermissions);
            $newPermissions = Str::replace('comment_real_name_verify', 'comment_required_kyc', $newPermissions);

            $permissionsArr = json_decode($newPermissions, true);

            $role->update([
                'rid' => $rid,
                'name' => $newName,
                'permissions' => $permissionsArr,
            ]);
        }

        if (Schema::hasColumn('roles', 'rid')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->json('name')->change();
                $table->string('rid', 32)->unique('rid')->change();
            });
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
