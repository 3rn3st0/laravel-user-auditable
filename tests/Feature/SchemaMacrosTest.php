<?php

namespace ErnestoCh\UserAuditable\Tests\Feature;

use ErnestoCh\UserAuditable\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SchemaMacrosTest extends TestCase
{
    public function test_user_auditable_macro_creates_columns()
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->id();
            $table->userAuditable();
        });

        $this->assertTrue(Schema::hasColumns('test_table', [
            'created_by', 'updated_by', 'deleted_by'
        ]));
    }

    public function test_full_auditable_macro_creates_all_columns()
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->id();
            $table->fullAuditable();
        });

        $this->assertTrue(Schema::hasColumns('test_table', [
            'created_at', 'updated_at', 'deleted_at',
            'created_by', 'updated_by', 'deleted_by'
        ]));
    }

    public function test_user_auditable_with_uuid()
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->id();
            $table->userAuditable('users', 'uuid');
        });

        $columns = Schema::getColumnType('test_table', 'created_by');
        $this->assertEquals('string', $columns); // UUID se almacena como string
    }

    public function test_drop_user_auditable_macro()
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->id();
            $table->userAuditable();
        });

        Schema::table('test_table', function (Blueprint $table) {
            $table->dropUserAuditable();
        });

        $this->assertFalse(Schema::hasColumns('test_table', [
            'created_by', 'updated_by', 'deleted_by'
        ]));
    }
}
