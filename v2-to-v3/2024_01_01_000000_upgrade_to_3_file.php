<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run fresns migrations.
     */
    public function up(): void
    {
        $files = [
            config_path('broadcasting.php'),
            config_path('cache.php'),
            config_path('cors.php'),
            config_path('filesystems.php'),
            config_path('hashing.php'),
            config_path('mail.php'),
            config_path('queue.php'),
            config_path('sanctum.php'),
            config_path('services.php'),
            config_path('view.php'),
            config_path('plugins.php'),
            config_path('themes.php'),
            config_path('markets.php'),
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
    }
};
