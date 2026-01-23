<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Item;
use App\Models\Land;
use App\Models\User;
use App\Models\Client;
use App\Models\Governorate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkActionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // ==================== ITEM BULK ACTIONS ====================

    public function test_bulk_delete_items_successfully()
    {
        $items = Item::factory()->count(3)->create();
        $ids = $items->pluck('id')->toArray();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.items.bulk-delete'), ['ids' => $ids]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        foreach ($ids as $id) {
            $this->assertSoftDeleted('items', ['id' => $id]);
        }
    }

    public function test_bulk_delete_items_requires_ids()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('admin.items.bulk-delete'), []);

        $response->assertStatus(422);
    }

    public function test_bulk_restore_items_successfully()
    {
        $items = Item::factory()->count(3)->create();
        $ids = $items->pluck('id')->toArray();

        // Soft delete items first
        Item::whereIn('id', $ids)->delete();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.items.bulk-restore'), ['ids' => $ids]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        foreach ($ids as $id) {
            $this->assertDatabaseHas('items', ['id' => $id, 'deleted_at' => null]);
        }
    }

    public function test_bulk_force_delete_items_successfully()
    {
        $items = Item::factory()->count(3)->create();
        $ids = $items->pluck('id')->toArray();

        // Soft delete items first
        Item::whereIn('id', $ids)->delete();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.items.bulk-force-delete'), ['ids' => $ids]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        foreach ($ids as $id) {
            $this->assertDatabaseMissing('items', ['id' => $id]);
        }
    }

    // ==================== LAND BULK ACTIONS ====================

    public function test_bulk_delete_lands_successfully()
    {
        $client = Client::factory()->create();
        $governorate = Governorate::factory()->create();
        $lands = Land::factory()->count(3)->create([
            'client_id' => $client->id,
            'governorate_id' => $governorate->id,
        ]);
        $ids = $lands->pluck('id')->toArray();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.lands.bulk-delete'), ['ids' => $ids]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        foreach ($ids as $id) {
            $this->assertSoftDeleted('lands', ['id' => $id]);
        }
    }

    public function test_bulk_delete_lands_requires_ids()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('admin.lands.bulk-delete'), []);

        $response->assertStatus(422);
    }

    public function test_bulk_restore_lands_successfully()
    {
        $client = Client::factory()->create();
        $governorate = Governorate::factory()->create();
        $lands = Land::factory()->count(3)->create([
            'client_id' => $client->id,
            'governorate_id' => $governorate->id,
        ]);
        $ids = $lands->pluck('id')->toArray();

        // Soft delete lands first
        Land::whereIn('id', $ids)->delete();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.lands.bulk-restore'), ['ids' => $ids]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        foreach ($ids as $id) {
            $this->assertDatabaseHas('lands', ['id' => $id, 'deleted_at' => null]);
        }
    }

    public function test_bulk_force_delete_lands_successfully()
    {
        $client = Client::factory()->create();
        $governorate = Governorate::factory()->create();
        $lands = Land::factory()->count(3)->create([
            'client_id' => $client->id,
            'governorate_id' => $governorate->id,
        ]);
        $ids = $lands->pluck('id')->toArray();

        // Soft delete lands first
        Land::whereIn('id', $ids)->delete();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.lands.bulk-force-delete'), ['ids' => $ids]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        foreach ($ids as $id) {
            $this->assertDatabaseMissing('lands', ['id' => $id]);
        }
    }

    public function test_restore_single_land_successfully()
    {
        $client = Client::factory()->create();
        $governorate = Governorate::factory()->create();
        $land = Land::factory()->create([
            'client_id' => $client->id,
            'governorate_id' => $governorate->id,
        ]);

        $land->delete();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.lands.restore', $land->id));

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('lands', ['id' => $land->id, 'deleted_at' => null]);
    }

    public function test_force_delete_single_land_successfully()
    {
        $client = Client::factory()->create();
        $governorate = Governorate::factory()->create();
        $land = Land::factory()->create([
            'client_id' => $client->id,
            'governorate_id' => $governorate->id,
        ]);

        $land->delete();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.lands.force-delete', $land->id));

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('lands', ['id' => $land->id]);
    }

    // ==================== FILE BULK ACTIONS ====================

    public function test_bulk_delete_files_successfully()
    {
        $client = Client::factory()->create();
        $files = File::factory()->count(3)->create([
            'client_id' => $client->id,
        ]);
        $ids = $files->pluck('id')->toArray();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.files.bulk-delete'), ['ids' => $ids]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        foreach ($ids as $id) {
            $this->assertSoftDeleted('files', ['id' => $id]);
        }
    }

    public function test_bulk_delete_files_requires_ids()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('admin.files.bulk-delete'), []);

        $response->assertStatus(422);
    }

    public function test_bulk_restore_files_successfully()
    {
        $client = Client::factory()->create();
        $files = File::factory()->count(3)->create([
            'client_id' => $client->id,
        ]);
        $ids = $files->pluck('id')->toArray();

        // Soft delete files first
        File::whereIn('id', $ids)->delete();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.files.bulk-restore'), ['ids' => $ids]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        foreach ($ids as $id) {
            $this->assertDatabaseHas('files', ['id' => $id, 'deleted_at' => null]);
        }
    }

    public function test_bulk_force_delete_files_successfully()
    {
        $client = Client::factory()->create();
        $files = File::factory()->count(3)->create([
            'client_id' => $client->id,
        ]);
        $ids = $files->pluck('id')->toArray();

        // Soft delete files first
        File::whereIn('id', $ids)->delete();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.files.bulk-force-delete'), ['ids' => $ids]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        foreach ($ids as $id) {
            $this->assertDatabaseMissing('files', ['id' => $id]);
        }
    }

    // ==================== VALIDATION TESTS ====================

    public function test_bulk_delete_items_with_invalid_ids()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('admin.items.bulk-delete'), ['ids' => [99999, 99998]]);

        $response->assertStatus(422);
    }

    public function test_bulk_delete_lands_with_invalid_ids()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('admin.lands.bulk-delete'), ['ids' => [99999, 99998]]);

        $response->assertStatus(422);
    }

    public function test_bulk_delete_files_with_invalid_ids()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('admin.files.bulk-delete'), ['ids' => [99999, 99998]]);

        $response->assertStatus(422);
    }

    // ==================== AUTH TESTS ====================

    public function test_bulk_delete_items_requires_authentication()
    {
        $response = $this->postJson(route('admin.items.bulk-delete'), ['ids' => [1]]);
        $response->assertUnauthorized();
    }

    public function test_bulk_delete_lands_requires_authentication()
    {
        $response = $this->postJson(route('admin.lands.bulk-delete'), ['ids' => [1]]);
        $response->assertUnauthorized();
    }

    public function test_bulk_delete_files_requires_authentication()
    {
        $response = $this->postJson(route('admin.files.bulk-delete'), ['ids' => [1]]);
        $response->assertUnauthorized();
    }
}
