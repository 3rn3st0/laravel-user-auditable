<?php

namespace ErnestoCh\UserAuditable\Tests;

use Illuminate\Support\Facades\DB;

trait DatabaseRefresh
{
    protected function refreshDatabase()
    {
        // MySQL only - get all tables
        $tables = DB::select('SHOW TABLES');

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            if (str_starts_with($tableName, 'test_')) {
                DB::statement("DROP TABLE IF EXISTS `{$tableName}`");
            }
        }

        // Reactivate foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
