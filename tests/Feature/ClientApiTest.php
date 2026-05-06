<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->user = User::factory()->create();
        $this->user->assignRole('administrator');
    }

    private function actingAsApi(): static
    {
        return $this->actingAs($this->user, 'sanctum');
    }

    // ---------- Index ----------

    public function test_api_index_requires_auth(): void
    {
        $this->getJson(route('api.v1.clients.index'))
            ->assertUnauthorized();
    }

    public function test_api_index_returns_paginated_clients(): void
    {
        Client::factory()->count(5)->create();

        $this->actingAsApi()
            ->getJson(route('api.v1.clients.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'name', 'email', 'phone', 'status', 'notes', 'created_at', 'updated_at']],
                'links',
                'meta',
            ]);
    }

    public function test_api_index_search(): void
    {
        Client::factory()->create(['name' => 'Alice']);
        Client::factory()->create(['name' => 'Bob']);

        $response = $this->actingAsApi()
            ->getJson(route('api.v1.clients.index', ['search' => 'Alice']))
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
    }

    // ---------- Store ----------

    public function test_api_store_creates_client(): void
    {
        $this->actingAsApi()
            ->postJson(route('api.v1.clients.store'), [
                'name'   => 'API Client',
                'email'  => 'api@example.com',
                'status' => 'active',
            ])
            ->assertCreated()
            ->assertJsonPath('data.email', 'api@example.com');

        $this->assertDatabaseHas('clients', ['email' => 'api@example.com']);
    }

    public function test_api_store_validates_required_fields(): void
    {
        $this->actingAsApi()
            ->postJson(route('api.v1.clients.store'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'status']);
    }

    // ---------- Show ----------

    public function test_api_show_returns_client(): void
    {
        $client = Client::factory()->create();

        $this->actingAsApi()
            ->getJson(route('api.v1.clients.show', $client))
            ->assertOk()
            ->assertJsonPath('data.id', $client->id);
    }

    public function test_api_show_returns_404_for_missing_client(): void
    {
        $this->actingAsApi()
            ->getJson(route('api.v1.clients.show', 99999))
            ->assertNotFound();
    }

    // ---------- Update ----------

    public function test_api_update_modifies_client(): void
    {
        $client = Client::factory()->create(['status' => 'active']);

        $this->actingAsApi()
            ->putJson(route('api.v1.clients.update', $client), [
                'name'   => 'Updated',
                'status' => 'inactive',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'inactive');
    }

    // ---------- Destroy ----------

    public function test_api_destroy_soft_deletes_client(): void
    {
        $client = Client::factory()->create();

        $this->actingAsApi()
            ->deleteJson(route('api.v1.clients.destroy', $client))
            ->assertOk()
            ->assertJsonPath('message', 'Client archived successfully.');

        $this->assertSoftDeleted('clients', ['id' => $client->id]);
    }

    public function test_api_store_forbids_user_without_clients_create_permission(): void
    {
        $clientUser = User::factory()->create();
        $clientUser->assignRole('client');

        $this->actingAs($clientUser, 'sanctum')
            ->postJson(route('api.v1.clients.store'), [
                'name' => 'Forbidden API Client',
                'status' => 'active',
            ])
            ->assertForbidden();
    }
}
