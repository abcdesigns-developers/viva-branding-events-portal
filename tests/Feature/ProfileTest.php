<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $this->actingAs(User::factory()->create())->get('/profile')->assertOk();
    }

    public function test_name_and_mobile_can_be_updated_but_email_cannot(): void
    {
        $user = User::factory()->create(['email' => 'keep@viva.test']);

        $this->actingAs($user)
            ->patch('/profile', ['name' => 'New Name', 'mobile' => '9876543210', 'email' => 'changed@viva.test'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();
        $this->assertSame('New Name', $user->name);
        $this->assertSame('9876543210', $user->mobile);
        $this->assertSame('keep@viva.test', $user->email);
    }

    public function test_guest_is_sent_to_login_from_home_and_profile(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/profile')->assertRedirect('/login');
    }

    public function test_register_and_verify_routes_are_gone(): void
    {
        $this->get('/register')->assertNotFound();
        $this->get('/verify-email')->assertNotFound();
    }

    public function test_profile_photo_can_be_uploaded_served_and_removed(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/profile/photo', ['photo' => UploadedFile::fake()->image('me.png', 600, 400)])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();
        $this->assertNotNull($user->profile_photo);
        Storage::disk('local')->assertExists($user->profile_photo);
        $this->actingAs($user)->get('/profile')->assertOk();
        $this->actingAs($user)->get(parse_url($user->photo_url, PHP_URL_PATH))->assertOk();

        $this->actingAs($user)->delete('/profile/photo')->assertRedirect('/profile');
        $this->assertNull($user->fresh()->profile_photo);
    }

    public function test_profile_photo_must_be_an_image(): void
    {
        Storage::fake('local');

        $this->actingAs(User::factory()->create())
            ->post('/profile/photo', ['photo' => UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf')])
            ->assertSessionHasErrors('photo');
    }
}
