<?php

namespace ErnestoCh\UserAuditable\Tests\Feature;

use ErnestoCh\UserAuditable\Tests\TestCase;
use ErnestoCh\UserAuditable\Tests\TestModels\TestUser;
use ErnestoCh\UserAuditable\Traits\UserAuditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

// Test model with SoftDeletes
class TestModelWithSoftDeletes extends Model
{
    use SoftDeletes, UserAuditable;

    protected $table = 'test_models_with_soft_deletes';
    protected $guarded = [];
}

// Test model without SoftDeletes
class TestModelWithoutSoftDeletes extends Model
{
    use UserAuditable;

    protected $table = 'test_models_without_soft_deletes';
    protected $guarded = [];
}

class UserAuditableTraitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create the users table first (with the exact name the macro expects)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Create test tables - explicitly reference the 'users' table
        Schema::create('test_models_with_soft_deletes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->userAuditable('users'); // Explicitly reference 'users' table
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('test_models_without_soft_deletes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->userAuditable('users'); // Explicitly reference 'users' table
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        // Clean tables in reverse order (respect foreign key constraints)
        Schema::dropIfExists('test_models_with_soft_deletes');
        Schema::dropIfExists('test_models_without_soft_deletes');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_automatically_sets_created_by()
    {
        $user = TestUser::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        Auth::login($user);

        $model = TestModelWithSoftDeletes::create(['name' => 'Test Model']);

        $this->assertEquals($user->id, $model->created_by);
        $this->assertNull($model->updated_by);
    }

    public function test_automatically_sets_updated_by()
    {
        $user = TestUser::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        Auth::login($user);

        $model = TestModelWithSoftDeletes::create(['name' => 'Test Model']);
        $model->update(['name' => 'Updated Model']);

        $this->assertEquals($user->id, $model->updated_by);
    }

    public function test_relationships_exist()
    {
        $model = new TestModelWithSoftDeletes();

        $this->assertTrue(method_exists($model, 'creator'));
        $this->assertTrue(method_exists($model, 'updater'));
        $this->assertTrue(method_exists($model, 'deleter'));
    }

    public function test_works_without_soft_deletes()
    {
        $user = TestUser::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        Auth::login($user);

        $model = TestModelWithoutSoftDeletes::create(['name' => 'Test Model']);
        $model->update(['name' => 'Updated Model']);

        $this->assertEquals($user->id, $model->created_by);
        $this->assertEquals($user->id, $model->updated_by);

        // It shouldn't fail even if you don't have SoftDeletes
        $model->delete();
        $this->assertTrue(true);
    }

    public function test_deleted_by_is_set_on_soft_delete()
    {
        $user = TestUser::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        Auth::login($user);

        $model = TestModelWithSoftDeletes::create(['name' => 'Test Model']);
        $model->delete();

        $this->assertEquals($user->id, $model->deleted_by);
        $this->assertNotNull($model->deleted_at);
    }
}
