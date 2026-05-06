<?php

namespace Tests\Unit;

use App\Models\User;
use App\Policies\UserPolicy;
use Mockery;
use PHPUnit\Framework\TestCase;

class UserPolicyTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_delete_denies_self_deletion_even_with_permission(): void
    {
        $actor = Mockery::mock(User::class);
        $managedUser = Mockery::mock(User::class);

        $actor->shouldReceive('can')->once()->with('USERS.DELETE')->andReturn(true);
        $actor->shouldReceive('is')->once()->with($managedUser)->andReturn(true);

        $this->assertFalse((new UserPolicy())->delete($actor, $managedUser));
    }

    public function test_delete_allows_other_user_when_permission_exists(): void
    {
        $actor = Mockery::mock(User::class);
        $managedUser = Mockery::mock(User::class);

        $actor->shouldReceive('can')->once()->with('USERS.DELETE')->andReturn(true);
        $actor->shouldReceive('is')->once()->with($managedUser)->andReturn(false);

        $this->assertTrue((new UserPolicy())->delete($actor, $managedUser));
    }
}