<?php

namespace Tests\Feature;

use App\Models\Photo;
use App\Models\PhotoGroup;
use App\Models\PhotoGroupItem;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EndToEndFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('public');
    }

    protected function createAdmin(): User
    {
        return User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);
    }

    /**
     * Test admin login with seeded credentials works and redirects to dashboard.
     */
    public function test_admin_login_with_seeded_credentials_redirects_to_dashboard(): void
    {
        $this->createAdmin();

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    /**
     * Test dashboard displays stats with zero counts initially.
     */
    public function test_dashboard_displays_zero_stats_initially(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas('totalPhotos', 0);
        $response->assertViewHas('totalVideos', 0);
        $response->assertViewHas('totalGroups', 0);
        $response->assertViewHas('activeGroups', 0);
    }

    /**
     * Test media library pages are accessible.
     */
    public function test_media_library_pages_are_accessible(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/photos');
        $response->assertStatus(200);
        $response->assertViewIs('admin.photos.index');

        $response = $this->actingAs($admin)->get('/videos');
        $response->assertStatus(200);
        $response->assertViewIs('admin.videos.index');
    }

    /**
     * Test photo group creation form is accessible.
     */
    public function test_photo_group_creation_form_is_accessible(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/photo-groups/create');

        $response->assertStatus(200);
        $response->assertViewIs('admin.groups.create');
    }

    /**
     * Test display page for non-existent group returns 404.
     */
    public function test_display_page_for_nonexistent_group_returns_not_found(): void
    {
        $response = $this->get('/display/999');

        $response->assertNotFound();
        $response->assertViewIs('display.show');
        $response->assertViewHas('error', 'Slideshow tidak tersedia');
    }

    /**
     * Test full end-to-end flow: upload photo, create group, add item, view slideshow.
     */
    public function test_full_end_to_end_flow(): void
    {
        $admin = $this->createAdmin();

        // Step 1: Login
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'password123',
        ]);
        $response->assertRedirect('/dashboard');

        // Step 2: Upload a photo
        $file = UploadedFile::fake()->image('test-photo.jpg', 1080, 1920);

        $response = $this->actingAs($admin)->post('/photos', [
            'file' => $file,
            'title' => 'Test Photo',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('photos.index'));
        $response->assertSessionHas('success');

        $photo = Photo::first();
        $this->assertNotNull($photo);
        $this->assertEquals('Test Photo', $photo->title);
        $this->assertTrue($photo->is_active);
        Storage::disk('public')->assertExists($photo->file_path);

        // Step 3: Create a photo group
        $response = $this->actingAs($admin)->post('/photo-groups', [
            'name' => 'Test Group',
            'description' => 'A test slideshow group',
            'is_active' => '1',
            'slide_duration' => 5,
            'sort_order' => 0,
        ]);

        $response->assertRedirect(route('photo-groups.index'));
        $response->assertSessionHas('success');

        $group = PhotoGroup::first();
        $this->assertNotNull($group);
        $this->assertEquals('Test Group', $group->name);
        $this->assertTrue($group->is_active);

        // Step 4: Add photo to group
        $response = $this->actingAs($admin)->post("/photo-groups/{$group->id}/items", [
            'photo_id' => $photo->id,
        ]);

        $response->assertRedirect(route('group-items.index', $group));
        $response->assertSessionHas('success');

        $item = PhotoGroupItem::first();
        $this->assertNotNull($item);
        $this->assertEquals($photo->id, $item->photo_id);
        $this->assertEquals($group->id, $item->photo_group_id);
        $this->assertTrue($item->is_active);

        // Step 5: View slideshow display
        $response = $this->get("/display/{$group->id}");

        $response->assertStatus(200);
        $response->assertViewIs('display.show');
        $response->assertViewHas('error', null);
        $response->assertViewHas('slides');

        $slides = $response->viewData('slides');
        $this->assertCount(1, $slides);
        $this->assertSame('photo', $slides[0]['type']);

        // Step 6: Verify dashboard stats updated
        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertViewHas('totalPhotos', 1);
        $response->assertViewHas('totalVideos', 0);
        $response->assertViewHas('totalGroups', 1);
        $response->assertViewHas('activeGroups', 1);
    }

    /**
     * Test display page for inactive group returns 404.
     */
    public function test_display_page_for_inactive_group_returns_not_found(): void
    {
        $group = PhotoGroup::create([
            'name' => 'Inactive Group',
            'is_active' => false,
            'slide_duration' => 5,
            'sort_order' => 0,
        ]);

        $response = $this->get("/display/{$group->id}");

        $response->assertNotFound();
        $response->assertViewIs('display.show');
        $response->assertViewHas('error', 'Slideshow tidak tersedia');
    }

    /**
     * Test display page for active group without active media returns 404.
     */
    public function test_display_page_for_active_group_without_media_returns_not_found(): void
    {
        $group = PhotoGroup::create([
            'name' => 'Empty Group',
            'is_active' => true,
            'slide_duration' => 5,
            'sort_order' => 0,
        ]);

        $response = $this->get("/display/{$group->id}");

        $response->assertNotFound();
        $response->assertViewIs('display.show');
        $response->assertViewHas('error', 'Slideshow tidak tersedia');
    }

    /**
     * Test that the admin seeder creates the expected user.
     */
    public function test_admin_seeder_creates_expected_user(): void
    {
        $this->seed(AdminSeeder::class);

        $admin = User::where('username', 'admin')->first();
        $this->assertNotNull($admin);
        $this->assertEquals('Admin', $admin->name);
    }

    /**
     * Test that the admin seeder is idempotent.
     */
    public function test_admin_seeder_is_idempotent(): void
    {
        $this->seed(AdminSeeder::class);
        $this->seed(AdminSeeder::class);

        $count = User::where('username', 'admin')->count();
        $this->assertEquals(1, $count);
    }
}
