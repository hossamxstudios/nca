<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ItemControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = ['items.view', 'items.create', 'items.edit', 'items.delete'];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $role->syncPermissions($permissions);

        $this->user = User::factory()->create();
        $this->user->assignRole('Super Admin');
    }

    public function test_items_index_page_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.items.index'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboards.admin.pages.items.index');
    }

    public function test_item_can_be_created(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('admin.items.store'), [
                'name' => 'عقد بيع',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('items', ['name' => 'عقد بيع']);
    }

    public function test_item_creation_requires_name(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('admin.items.store'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_item_name_must_be_unique(): void
    {
        Item::factory()->create(['name' => 'عقد بيع']);

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.items.store'), ['name' => 'عقد بيع']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_item_can_be_updated(): void
    {
        $item = Item::factory()->create(['name' => 'الاسم القديم']);

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.items.update', $item), [
                'name' => 'الاسم الجديد',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('items', ['id' => $item->id, 'name' => 'الاسم الجديد']);
    }

    public function test_item_can_be_soft_deleted(): void
    {
        $item = Item::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.items.destroy', $item));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertSoftDeleted('items', ['id' => $item->id]);
    }

    public function test_item_can_be_restored(): void
    {
        $item = Item::factory()->create();
        $item->delete();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.items.restore', $item->id));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('items', ['id' => $item->id, 'deleted_at' => null]);
    }

    public function test_item_can_be_force_deleted(): void
    {
        $item = Item::factory()->create();
        $item->delete();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.items.force-delete', $item->id));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }

    public function test_items_can_be_searched(): void
    {
        Item::factory()->create(['name' => 'عقد بيع']);
        Item::factory()->create(['name' => 'شهادة ميلاد']);

        $response = $this->actingAs($this->user)
            ->get(route('admin.items.index', ['search' => 'عقد']));

        $response->assertStatus(200);
        $response->assertSee('عقد بيع');
    }

    public function test_trashed_items_can_be_filtered(): void
    {
        $activeItem = Item::factory()->create(['name' => 'نشط']);
        $trashedItem = Item::factory()->create(['name' => 'محذوف']);
        $trashedItem->delete();

        $response = $this->actingAs($this->user)
            ->get(route('admin.items.index', ['trashed' => 'only']));

        $response->assertStatus(200);
        $response->assertSee('محذوف');
    }
}
