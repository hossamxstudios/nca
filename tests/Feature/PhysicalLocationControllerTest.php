<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\Lane;
use App\Models\Stand;
use App\Models\Rack;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PhysicalLocationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = ['physical-locations.view', 'physical-locations.create', 'physical-locations.edit', 'physical-locations.delete'];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $role->syncPermissions($permissions);

        $this->user = User::factory()->create();
        $this->user->assignRole('Super Admin');
    }

    public function test_physical_locations_index_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.physical-locations.index'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboards.admin.pages.physical-locations.index');
    }

    public function test_room_can_be_created(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('admin.physical-locations.rooms.store'), [
                'name' => 'غرفة الأرشيف الرئيسية',
                'building_name' => 'المبنى الرئيسي',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('rooms', ['name' => 'غرفة الأرشيف الرئيسية']);
    }

    public function test_lane_can_be_created(): void
    {
        $room = Room::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.physical-locations.lanes.store'), [
                'name' => 'ممر 1',
                'room_id' => $room->id,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('lanes', ['name' => 'ممر 1', 'room_id' => $room->id]);
    }

    public function test_stand_can_be_created(): void
    {
        $room = Room::factory()->create();
        $lane = Lane::factory()->create(['room_id' => $room->id]);

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.physical-locations.stands.store'), [
                'name' => 'حامل 1',
                'lane_id' => $lane->id,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('stands', ['name' => 'حامل 1', 'lane_id' => $lane->id]);
    }

    public function test_rack_can_be_created(): void
    {
        $room = Room::factory()->create();
        $lane = Lane::factory()->create(['room_id' => $room->id]);
        $stand = Stand::factory()->create(['lane_id' => $lane->id]);

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.physical-locations.racks.store'), [
                'name' => 'رف 1',
                'stand_id' => $stand->id,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('racks', ['name' => 'رف 1', 'stand_id' => $stand->id]);
    }

    public function test_room_can_be_updated(): void
    {
        $room = Room::factory()->create(['name' => 'غرفة قديمة']);

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.physical-locations.rooms.update', $room), [
                'name' => 'غرفة جديدة',
                'building_name' => $room->building_name,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('rooms', ['id' => $room->id, 'name' => 'غرفة جديدة']);
    }

    public function test_room_can_be_deleted(): void
    {
        $room = Room::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.physical-locations.rooms.destroy', $room));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertSoftDeleted('rooms', ['id' => $room->id]);
    }

    public function test_lane_can_be_deleted(): void
    {
        $room = Room::factory()->create();
        $lane = Lane::factory()->create(['room_id' => $room->id]);

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.physical-locations.lanes.destroy', $lane));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertSoftDeleted('lanes', ['id' => $lane->id]);
    }

    public function test_stand_can_be_deleted(): void
    {
        $room = Room::factory()->create();
        $lane = Lane::factory()->create(['room_id' => $room->id]);
        $stand = Stand::factory()->create(['lane_id' => $lane->id]);

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.physical-locations.stands.destroy', $stand));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertSoftDeleted('stands', ['id' => $stand->id]);
    }

    public function test_rack_can_be_deleted(): void
    {
        $room = Room::factory()->create();
        $lane = Lane::factory()->create(['room_id' => $room->id]);
        $stand = Stand::factory()->create(['lane_id' => $lane->id]);
        $rack = Rack::factory()->create(['stand_id' => $stand->id]);

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.physical-locations.racks.destroy', $rack));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertSoftDeleted('racks', ['id' => $rack->id]);
    }

    public function test_room_creation_validates_required_name(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('admin.physical-locations.rooms.store'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_lane_creation_requires_room_id(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('admin.physical-locations.lanes.store'), [
                'name' => 'ممر 1',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['room_id']);
    }
}
