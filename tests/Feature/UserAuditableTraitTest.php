<?php

namespace ErnestoCh\UserAuditable\Tests\Feature;

use ErnestoCh\UserAuditable\Tests\TestCase;
use ErnestoCh\UserAuditable\Traits\UserAuditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class UserAuditableTraitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Crear tabla de prueba
        Schema::create('test_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->userAuditable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Crear tabla de usuarios
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Modelo de prueba
        $this->testModel = new class extends Model {
            use UserAuditable;
            protected $table = 'test_models';
            protected $guarded = [];
        };

        // User model
        $this->userModel = new class extends Model {
            protected $table = 'users';
            protected $guarded = [];
        };
    }

    public function test_automatically_sets_created_by()
    {
        $user = $this->userModel->create(['id' => 1, 'name' => 'Test User']);
        Auth::login($user);

        $model = $this->testModel->create(['name' => 'Test Model']);

        $this->assertEquals(1, $model->created_by);
        $this->assertNull($model->updated_by);
    }

    public function test_automatically_sets_updated_by()
    {
        $user = $this->userModel->create(['id' => 1, 'name' => 'Test User']);
        Auth::login($user);

        $model = $this->testModel->create(['name' => 'Test Model']);
        $model->update(['name' => 'Updated Model']);

        $this->assertEquals(1, $model->updated_by);
    }

    public function test_relationships_exist()
    {
        $model = new $this->testModel();

        $this->assertTrue(method_exists($model, 'creator'));
        $this->assertTrue(method_exists($model, 'updater'));
        $this->assertTrue(method_exists($model, 'deleter'));
    }
}
