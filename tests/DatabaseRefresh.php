<?php
// tests/DatabaseRefresh.php

namespace ErnestoCh\UserAuditable\Tests;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait DatabaseRefresh
{
    protected function refreshDatabase()
    {
        $connection = config('database.default');

        if ($connection === 'mysql') {
            // MySQL implementation
            $tables = DB::select('SHOW TABLES');
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($tables as $table) {
                $tableName = array_values((array)$table)[0];
                if (str_starts_with($tableName, 'test_')) {
                    DB::statement("DROP TABLE IF EXISTS `{$tableName}`");
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } elseif ($connection === 'sqlite') {
            // SQLite implementation - drop all test tables
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name LIKE 'test_%'");

            foreach ($tables as $table) {
                Schema::dropIfExists($table->name);
            }

            // Also drop users table if exists
            Schema::dropIfExists('users');
            Schema::dropIfExists('users_uuid');
        }
    }
}
