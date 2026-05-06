<?php

namespace Tests\Feature;

use App\Mail\MagicLinkMail;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('administrator');
    }

    public function test_user_without_users_read_cannot_access_users_index(): void
    {
        $clientUser = User::factory()->create();
        $clientUser->assignRole('client');

        $this->actingAs($clientUser)
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_list_users(): void
    {
        $this->actingAs($this->admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Users/Index')
                ->has('users.data')
            );
    }

    public function test_admin_can_create_user_without_sending_invite(): void
    {
        Mail::fake();

        $roleId = (int) $this->admin->roles()->firstOrFail()->id;

        $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'name' => 'Managed User',
                'email' => 'managed@example.com',
                'roles' => [$roleId],
                'send_invite' => false,
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', ['email' => 'managed@example.com']);
        Mail::assertNothingSent();
    }

    public function test_admin_can_create_user_and_send_invite(): void
    {
        Mail::fake();

        $roleId = (int) $this->admin->roles()->firstOrFail()->id;

        $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'name' => 'Invited User',
                'email' => 'invited@example.com',
                'roles' => [$roleId],
                'send_invite' => true,
            ])
            ->assertRedirect(route('users.index'));

        Mail::assertSent(MagicLinkMail::class);
    }

    public function test_admin_can_update_user_roles(): void
    {
        $managedUser = User::factory()->create();
        $clientRoleId = (int) \Spatie\Permission\Models\Role::findByName('client', 'web')->id;

        $this->actingAs($this->admin)
            ->put(route('users.update', $managedUser), [
                'name' => 'Updated User',
                'email' => $managedUser->email,
                'roles' => [$clientRoleId],
            ])
            ->assertRedirect(route('users.index'));

        $managedUser->refresh();

        $this->assertSame(['client'], $managedUser->getRoleNames()->values()->all());
    }

    public function test_admin_can_delete_another_user(): void
    {
        $managedUser = User::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('users.destroy', $managedUser))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('users', ['id' => $managedUser->id]);
    }

    public function test_admin_cannot_delete_own_account_via_user_management(): void
    {
        $this->actingAs($this->admin)
            ->delete(route('users.destroy', $this->admin))
            ->assertForbidden();
    }
}