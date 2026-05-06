<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_client_role_user_has_read_only_client_access_on_web(): void
    {
        $user = User::factory()->create();
        $user->assignRole('client');
        $client = Client::factory()->create();

        $this->actingAs($user)
            ->get(route('clients.index'))
            ->assertOk();

        $this->actingAs($user)
            ->post(route('clients.store'), [
                'name' => 'Forbidden Client',
                'status' => 'active',
            ])
            ->assertForbidden();

        $this->actingAs($user)
            ->delete(route('clients.destroy', $client))
            ->assertForbidden();
    }

    public function test_administrator_role_user_has_full_client_access_on_web(): void
    {
        $user = User::factory()->create();
        $user->assignRole('administrator');
        $client = Client::factory()->create();

        $this->actingAs($user)
            ->post(route('clients.store'), [
                'name' => 'Allowed Client',
                'status' => 'active',
            ])
            ->assertRedirect(route('clients.index'));

        $this->actingAs($user)
            ->delete(route('clients.destroy', $client))
            ->assertRedirect(route('clients.index'));
    }

    public function test_client_role_user_has_read_only_client_access_on_api(): void
    {
        $user = User::factory()->create();
        $user->assignRole('client');
        $client = Client::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson(route('api.v1.clients.index'))
            ->assertOk();

        $this->actingAs($user, 'sanctum')
            ->postJson(route('api.v1.clients.store'), [
                'name' => 'Forbidden Client',
                'status' => 'active',
            ])
            ->assertForbidden();

        $this->actingAs($user, 'sanctum')
            ->deleteJson(route('api.v1.clients.destroy', $client))
            ->assertForbidden();
    }

    public function test_inertia_shared_auth_props_include_roles_and_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('client');

        $this->actingAs($user)
            ->get(route('clients.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('auth.roles', ['client'])
                ->where('auth.permissions', ['CLIENTS.READ'])
            );
    }
}