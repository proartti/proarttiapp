<?php

namespace Tests\Unit;

use App\Models\User;
use App\Policies\RolePolicy;
use Mockery;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePolicyTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_delete_denies_deleting_administrator_role(): void
    {
        $actor = Mockery::mock(User::class);
        $role = new Role(['name' => 'administrator']);

        $actor->shouldReceive('can')->once()->with('ROLES.DELETE')->andReturn(true);

        $this->assertFalse((new RolePolicy())->delete($actor, $role));
    }

    public function test_delete_allows_other_role_when_permission_exists(): void
    {
        $actor = Mockery::mock(User::class);
        $role = new Role(['name' => 'editor']);

        $actor->shouldReceive('can')->once()->with('ROLES.DELETE')->andReturn(true);

        $this->assertTrue((new RolePolicy())->delete($actor, $role));
    }
}