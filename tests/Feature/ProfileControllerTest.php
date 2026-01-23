<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'first_name' => 'أحمد',
            'last_name' => 'محمد',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_profile_page_is_displayed(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.profile.index'));

        $response->assertOk();
        $response->assertViewIs('dashboards.admin.pages.profile.index');
        $response->assertViewHas('user');
    }

    public function test_profile_information_can_be_updated(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.profile.update'), [
            'first_name' => 'محمد',
            'last_name' => 'علي',
            'email' => 'newemail@example.com',
            'phone' => '01234567890',
            'job_title' => 'مدير النظام',
            'department' => 'تكنولوجيا المعلومات',
            'bio' => 'نبذة تجريبية',
        ]);

        $response->assertRedirect(route('admin.profile.index'));
        $response->assertSessionHas('success');

        $this->user->refresh();

        $this->assertEquals('محمد', $this->user->first_name);
        $this->assertEquals('علي', $this->user->last_name);
        $this->assertEquals('newemail@example.com', $this->user->email);
        $this->assertEquals('01234567890', $this->user->phone);
        $this->assertEquals('مدير النظام', $this->user->job_title);
        $this->assertEquals('تكنولوجيا المعلومات', $this->user->department);
        $this->assertEquals('نبذة تجريبية', $this->user->bio);
        $this->assertNull($this->user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_email_is_unchanged(): void
    {
        $this->user->email_verified_at = now();
        $this->user->save();

        $response = $this->actingAs($this->user)->post(route('admin.profile.update'), [
            'first_name' => 'محمد',
            'last_name' => 'علي',
            'email' => $this->user->email,
        ]);

        $this->user->refresh();

        $this->assertNotNull($this->user->email_verified_at);
    }

    public function test_profile_update_validation_fails_with_invalid_data(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.profile.update'), [
            'first_name' => '',
            'last_name' => '',
            'email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors(['first_name', 'last_name', 'email']);
    }

    public function test_user_can_update_password(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.profile.password'), [
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('admin.profile.index'));
        $response->assertSessionHas('success');

        $this->user->refresh();

        $this->assertTrue(Hash::check('newpassword123', $this->user->password));
    }

    public function test_password_update_fails_with_wrong_current_password(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.profile.password'), [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors(['current_password']);
    }

    public function test_password_update_fails_without_confirmation(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.profile.password'), [
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_user_can_upload_avatar(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($this->user)->post(route('admin.profile.update'), [
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'email' => $this->user->email,
            'avatar' => $file,
        ]);

        $response->assertRedirect(route('admin.profile.index'));
        $response->assertSessionHas('success');

        $this->user->refresh();

        $this->assertNotNull($this->user->getFirstMediaUrl('avatar'));
    }

    public function test_user_can_remove_avatar(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.jpg');
        $this->user->addMedia($file)->toMediaCollection('avatar');

        $this->assertNotNull($this->user->getFirstMediaUrl('avatar'));

        $response = $this->actingAs($this->user)->post(route('admin.profile.remove-avatar'));

        $response->assertRedirect(route('admin.profile.index'));
        $response->assertSessionHas('success');

        $this->user->refresh();

        $this->assertEmpty($this->user->getFirstMediaUrl('avatar'));
    }

    public function test_avatar_upload_validation_fails_with_invalid_file(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 1000);

        $response = $this->actingAs($this->user)->post(route('admin.profile.update'), [
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'email' => $this->user->email,
            'avatar' => $file,
        ]);

        $response->assertSessionHasErrors(['avatar']);
    }

    public function test_avatar_upload_validation_fails_with_large_file(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.jpg')->size(3000);

        $response = $this->actingAs($this->user)->post(route('admin.profile.update'), [
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'email' => $this->user->email,
            'avatar' => $file,
        ]);

        $response->assertSessionHasErrors(['avatar']);
    }

    public function test_profile_displays_user_roles_and_permissions(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.profile.index'));

        $response->assertOk();
        $response->assertSee('الأدوار والصلاحيات');
    }
}
