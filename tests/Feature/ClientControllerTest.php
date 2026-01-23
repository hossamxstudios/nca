<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use App\Models\Governorate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        $permissions = [
            'clients.view', 'clients.create', 'clients.edit', 'clients.delete', 'clients.export',
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create role with permissions
        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $role->syncPermissions($permissions);

        // Create user with role
        $this->user = User::factory()->create();
        $this->user->assignRole('Super Admin');
    }

    public function test_clients_index_page_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.clients.index'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboards.admin.pages.clients.index');
    }

    public function test_clients_index_shows_clients(): void
    {
        Client::factory()->count(5)->create();

        $response = $this->actingAs($this->user)->get(route('admin.clients.index'));
        $response->assertStatus(200);
        $response->assertViewHas('clients');
    }

    public function test_clients_can_be_searched(): void
    {
        $client = Client::factory()->create(['name' => 'محمد أحمد']);
        Client::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->get(route('admin.clients.index', ['search' => 'محمد']));
        $response->assertStatus(200);
        $response->assertSee('محمد أحمد');
    }

    public function test_client_can_be_created(): void
    {
        $clientData = [
            'name' => 'عميل جديد',
            'national_id' => '12345678901234',
            'mobile' => '01012345678',
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.clients.store'), $clientData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('clients', ['name' => 'عميل جديد']);
    }

    public function test_client_creation_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('admin.clients.store'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_client_creation_validates_national_id_length(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('admin.clients.store'), [
                'name' => 'عميل',
                'national_id' => '123', // Should be 14 digits
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['national_id']);
    }

    public function test_client_creation_validates_unique_national_id(): void
    {
        Client::factory()->create(['national_id' => '12345678901234']);

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.clients.store'), [
                'name' => 'عميل آخر',
                'national_id' => '12345678901234',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['national_id']);
    }

    public function test_client_can_be_shown(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->user)
            ->getJson(route('admin.clients.show', $client));

        $response->assertStatus(200);
        $response->assertJsonStructure(['html']);
    }

    public function test_client_can_be_updated(): void
    {
        $client = Client::factory()->create(['name' => 'الاسم القديم']);

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.clients.update', $client), [
                'name' => 'الاسم الجديد',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('clients', ['id' => $client->id, 'name' => 'الاسم الجديد']);
    }

    public function test_client_can_be_soft_deleted(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.clients.destroy', $client));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertSoftDeleted('clients', ['id' => $client->id]);
    }

    public function test_client_can_be_restored(): void
    {
        $client = Client::factory()->create();
        $client->delete();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.clients.restore', $client->id));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('clients', ['id' => $client->id, 'deleted_at' => null]);
    }

    public function test_client_can_be_force_deleted(): void
    {
        $client = Client::factory()->create();
        $client->delete();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.clients.force-delete', $client->id));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    public function test_clients_can_be_bulk_deleted(): void
    {
        $clients = Client::factory()->count(3)->create();
        $ids = $clients->pluck('id')->toArray();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.clients.bulk-delete'), ['ids' => $ids]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        foreach ($ids as $id) {
            $this->assertSoftDeleted('clients', ['id' => $id]);
        }
    }

    public function test_clients_can_be_bulk_restored(): void
    {
        $clients = Client::factory()->count(3)->create();
        foreach ($clients as $client) {
            $client->delete();
        }
        $ids = $clients->pluck('id')->toArray();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.clients.bulk-restore'), ['ids' => $ids]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        foreach ($ids as $id) {
            $this->assertDatabaseHas('clients', ['id' => $id, 'deleted_at' => null]);
        }
    }

    public function test_trashed_clients_can_be_viewed(): void
    {
        Client::factory()->count(3)->create();
        $trashedClient = Client::factory()->create();
        $trashedClient->delete();

        $response = $this->actingAs($this->user)
            ->get(route('admin.clients.index', ['trashed' => 'only']));

        $response->assertStatus(200);
        $response->assertSee($trashedClient->name);
    }

    public function test_generate_client_code(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('admin.clients.generate-code'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['code']);
        $this->assertStringStartsWith('NCA-', $response->json('code'));
    }
}
