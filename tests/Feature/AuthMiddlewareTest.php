<?php

namespace Tests\Feature;

use App\Models\Photo;
use App\Models\PhotoGroup;
use App\Models\PhotoGroupItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_unauthenticated_user_is_redirected_from_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_is_redirected_from_photos_index(): void
    {
        $response = $this->get('/photos');

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_is_redirected_from_running_texts(): void
    {
        $response = $this->get('/running-texts');

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_is_redirected_from_photo_groups_index(): void
    {
        $response = $this->get('/photo-groups');

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_is_redirected_from_photo_groups_create(): void
    {
        $response = $this->get('/photo-groups/create');

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_is_redirected_from_group_items(): void
    {
        $response = $this->get('/photo-groups/1/items');

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_is_redirected_from_logout(): void
    {
        $response = $this->post('/logout');

        $response->assertRedirect('/login');
    }

    public function test_login_page_is_accessible_without_auth(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_display_page_is_accessible_without_auth(): void
    {
        $group = PhotoGroup::create([
            'name' => 'Public Group',
            'is_active' => true,
            'slide_duration' => 4,
            'sort_order' => 0,
        ]);

        $photo = Photo::create([
            'title' => 'Foto Publik',
            'file_path' => 'photos/publik.jpg',
            'type' => 'photo',
            'is_active' => true,
        ]);

        PhotoGroupItem::create([
            'photo_group_id' => $group->id,
            'photo_id' => $photo->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->get("/display/{$group->id}");

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }
}
