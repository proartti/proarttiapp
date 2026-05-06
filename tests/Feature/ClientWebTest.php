<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientWebTest extends TestCase
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

    // ---------- Index ----------

    public function test_index_requires_auth(): void
    {
        $this->get(route('clients.index'))->assertRedirect(route('login'));
    }

    public function test_index_lists_clients(): void
    {
        Client::factory()->count(3)->create();

        $this->actingAs($this->user)
            ->get(route('clients.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Clients/Index')
                ->has('clients.data', 3)
            );
    }

    public function test_index_search_filters_results(): void
    {
        Client::factory()->create(['name' => 'Alice Smith']);
        Client::factory()->create(['name' => 'Bob Jones']);

        $this->actingAs($this->user)
            ->get(route('clients.index', ['search' => 'Alice']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('clients.data', 1)
            );
    }

    // ---------- Create ----------

    public function test_create_page_renders(): void
    {
        $this->actingAs($this->user)
            ->get(route('clients.create'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Clients/Create'));
    }

    // ---------- Store ----------

    public function test_store_creates_client_and_redirects(): void
    {
        $this->actingAs($this->user)
            ->post(route('clients.store'), [
                'name'   => 'Test Client',
                'email'  => 'client@example.com',
                'phone'  => '555-0100',
                'status' => 'active',
                'notes'  => 'Some notes',
            ])
            ->assertRedirect(route('clients.index'));

        $this->assertDatabaseHas('clients', ['email' => 'client@example.com']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->post(route('clients.store'), [])
            ->assertSessionHasErrors(['name', 'status']);
    }

    public function test_store_rejects_duplicate_email(): void
    {
        Client::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs($this->user)
            ->post(route('clients.store'), [
                'name'   => 'New Client',
                'email'  => 'taken@example.com',
                'status' => 'active',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_store_forbids_user_without_clients_create_permission(): void
    {
        $clientUser = User::factory()->create();
        $clientUser->assignRole('client');

        $this->actingAs($clientUser)
            ->post(route('clients.store'), [
                'name' => 'Forbidden Client',
                'status' => 'active',
            ])
            ->assertForbidden();
    }

    // ---------- Edit ----------

    public function test_edit_page_renders(): void
    {
        $client = Client::factory()->create();

        $this->actingAs($this->user)
            ->get(route('clients.edit', $client))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Clients/Edit')
                ->where('client.id', $client->id)
            );
    }

    // ---------- Update ----------

    public function test_update_persists_changes(): void
    {
        $client = Client::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->user)
            ->put(route('clients.update', $client), [
                'name'   => 'New Name',
                'status' => 'inactive',
            ])
            ->assertRedirect(route('clients.index'));

        $this->assertDatabaseHas('clients', ['id' => $client->id, 'name' => 'New Name']);
    }

    public function test_update_allows_same_email_on_self(): void
    {
        $client = Client::factory()->create(['email' => 'same@example.com']);

        $this->actingAs($this->user)
            ->put(route('clients.update', $client), [
                'name'   => $client->name,
                'email'  => 'same@example.com',
                'status' => $client->status,
            ])
            ->assertRedirect(route('clients.index'));
    }

    // ---------- Destroy ----------

    public function test_destroy_soft_deletes_client(): void
    {
        $client = Client::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('clients.destroy', $client))
            ->assertRedirect(route('clients.index'));

        $this->assertSoftDeleted('clients', ['id' => $client->id]);
    }

    public function test_destroy_forbids_user_without_clients_delete_permission(): void
    {
        $client = Client::factory()->create();
        $clientUser = User::factory()->create();
        $clientUser->assignRole('client');

        $this->actingAs($clientUser)
            ->delete(route('clients.destroy', $client))
            ->assertForbidden();
    }
}
