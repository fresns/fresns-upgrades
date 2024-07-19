<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

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
            'sessions',
        ]);

        if (! Schema::hasColumn('session_logs', 'login_token')) {
            Schema::table('session_logs', function (Blueprint $table) {
                $table->string('login_token', 64)->nullable()->index('account_login_token')->after('user_id');
            });
        }

        Schema::table('session_tokens', function (Blueprint $table) {
            $table->string('account_token', 64)->change();
            $table->string('user_token', 64)->nullable()->change();
        });

        if (Schema::hasColumn('session_logs', 'object_name')) {
            Schema::table('session_logs', function (Blueprint $table) {
                $table->renameColumn('object_name', 'action_name');
                $table->renameColumn('object_action', 'action_desc');
                $table->renameColumn('object_result', 'action_state');
                $table->renameColumn('object_order_id', 'action_id');
            });
        }

        try {
            Schema::table('session_logs', function (Blueprint $table) {
                $table->index('action_state', 'log_action_state');
            });
        } catch (\Exception $e) {}

        if (! Schema::hasTable('job_batches')) {
            Schema::create('job_batches', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('name');
                $table->integer('total_jobs');
                $table->integer('pending_jobs');
                $table->integer('failed_jobs');
                $table->longText('failed_job_ids');
                $table->mediumText('options')->nullable();
                $table->integer('cancelled_at')->nullable();
                $table->integer('created_at');
                $table->integer('finished_at')->nullable();
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
