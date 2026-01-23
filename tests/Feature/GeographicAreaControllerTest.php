<?php

namespace Tests\Feature;

use App\Models\Governorate;
use App\Models\City;
use App\Models\District;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class GeographicAreaControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = ['geographic-areas.view', 'geographic-areas.create', 'geographic-areas.edit', 'geographic-areas.delete'];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $role->syncPermissions($permissions);

        $this->user = User::factory()->create();
        $this->user->assignRole('Super Admin');
    }

    public function test_geographic_areas_index_is_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.geographic-areas.index'));
        $response->assertStatus(200);
        $response->assertViewIs('dashboards.admin.pages.geographic-areas.index');
    }

    public function test_governorate_can_be_created(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('admin.geographic-areas.governorates.store'), [
                'name' => 'محافظة القاهرة',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('governorates', ['name' => 'محافظة القاهرة']);
    }

    public function test_city_can_be_created(): void
    {
        $governorate = Governorate::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.geographic-areas.cities.store'), [
                'name' => 'مدينة نصر',
                'governorate_id' => $governorate->id,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('cities', ['name' => 'مدينة نصر', 'governorate_id' => $governorate->id]);
    }

    public function test_district_can_be_created(): void
    {
        $governorate = Governorate::factory()->create();
        $city = City::factory()->create(['governorate_id' => $governorate->id]);

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.geographic-areas.districts.store'), [
                'name' => 'الحي الأول',
                'city_id' => $city->id,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('districts', ['name' => 'الحي الأول', 'city_id' => $city->id]);
    }

    public function test_governorate_can_be_updated(): void
    {
        $governorate = Governorate::factory()->create(['name' => 'القاهرة']);

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.geographic-areas.governorates.update', $governorate), [
                'name' => 'الجيزة',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('governorates', ['id' => $governorate->id, 'name' => 'الجيزة']);
    }

    public function test_governorate_can_be_deleted(): void
    {
        $governorate = Governorate::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.geographic-areas.governorates.destroy', $governorate));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertSoftDeleted('governorates', ['id' => $governorate->id]);
    }

    public function test_city_can_be_deleted(): void
    {
        $governorate = Governorate::factory()->create();
        $city = City::factory()->create(['governorate_id' => $governorate->id]);

        $response = $this->actingAs($this->user)
            ->postJson(route('admin.geographic-areas.cities.destroy', $city));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertSoftDeleted('cities', ['id' => $city->id]);
    }

    public function test_governorate_creation_validates_name(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('admin.geographic-areas.governorates.store'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_city_creation_requires_governorate(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('admin.geographic-areas.cities.store'), [
                'name' => 'مدينة نصر',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['governorate_id']);
    }

    public function test_cities_cascade_from_governorate(): void
    {
        $governorate = Governorate::factory()->create();
        City::factory()->count(3)->create(['governorate_id' => $governorate->id]);

        $response = $this->actingAs($this->user)
            ->getJson(route('admin.geographic-areas.cities.by-governorate', $governorate));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }
}
