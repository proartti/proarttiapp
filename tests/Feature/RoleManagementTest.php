<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleManagementTest extends TestCase
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

    public function test_user_without_roles_read_cannot_access_roles_index(): void
    {
        $clientUser = User::factory()->create();
        $clientUser->assignRole('client');

        $this->actingAs($clientUser)
            ->get(route('roles.index'))
            ->assertForbidden();
    }

    public function test_admin_can_list_roles(): void
    {
        $this->actingAs($this->admin)
            ->get(route('roles.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Roles/Index')
                ->has('roles.data')
            );
    }

    public function test_admin_can_create_role_with_permissions(): void
    {
        $permissionIds = Permission::query()
            ->whereIn('name', ['CLIENTS.CREATE', 'CLIENTS.READ'])
            ->pluck('id')
            ->all();

        $this->actingAs($this->admin)
            ->post(route('roles.store'), [
                'name' => 'editor',
                'permissions' => $permissionIds,
            ])
            ->assertRedirect(route('roles.index'));

        $role = Role::findByName('editor', 'web');

        $this->assertSame(
            ['CLIENTS.CREATE', 'CLIENTS.READ'],
            $role->permissions()->orderBy('name')->pluck('name')->all(),
        );
    }

    public function test_admin_can_update_role(): void
    {
        $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $permissionIds = Permission::query()
            ->whereIn('name', ['CLIENTS.READ', 'CLIENTS.UPDATE'])
            ->pluck('id')
            ->all();

        $this->actingAs($this->admin)
            ->put(route('roles.update', $role), [
                'name' => 'editor-updated',
                'permissions' => $permissionIds,
            ])
            ->assertRedirect(route('roles.index'));

        $role->refresh();

        $this->assertSame('editor-updated', $role->name);
    }

    public function test_admin_cannot_delete_administrator_role(): void
    {
        $role = Role::findByName('administrator', 'web');

        $this->actingAs($this->admin)
            ->delete(route('roles.destroy', $role))
            ->assertForbidden();
    }
}