<?php

namespace Tests\Feature;

use App\Models\Photo;
use App\Models\PhotoGroup;
use App\Models\PhotoGroupItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaUploadUxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('public');
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);
    }

    public function test_ajax_photo_upload_returns_card_payload(): void
    {
        $response = $this->actingAs($this->admin())
            ->withHeaders([
                'X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])
            ->post('/photos', [
            'file' => UploadedFile::fake()->image('lobby.jpg', 1080, 1920),
            'title' => 'Lobby Portrait',
            'is_active' => '1',
        ]);

        $response->assertOk()
            ->assertJsonPath('media.title', 'Lobby Portrait')
            ->assertJsonPath('media.type', 'photo')
            ->assertJsonPath('media.is_active', true)
            ->assertJsonStructure([
                'success',
                'message',
                'media' => ['id', 'title', 'type', 'url', 'edit_url', 'delete_url', 'is_active'],
            ]);
    }

    public function test_ajax_video_upload_returns_card_payload(): void
    {
        $response = $this->actingAs($this->admin())
            ->withHeaders([
                'X-Requested-With' => 'XMLHttpRequest',
                'Accept' => 'application/json',
            ])
            ->post('/videos', [
            'file' => UploadedFile::fake()->create('promo.mp4', 128, 'video/mp4'),
            'title' => 'Promo Loop',
            'is_active' => '1',
        ]);

        $response->assertOk()
            ->assertJsonPath('media.title', 'Promo Loop')
            ->assertJsonPath('media.type', 'video')
            ->assertJsonPath('media.is_active', true)
            ->assertJsonStructure([
                'success',
                'message',
                'media' => ['id', 'title', 'type', 'url', 'edit_url', 'delete_url', 'is_active'],
            ]);
    }

    public function test_display_page_has_no_visible_audio_hint(): void
    {
        $group = PhotoGroup::create([
            'name' => 'Lobby Display',
            'is_active' => true,
            'slide_duration' => 5,
            'sort_order' => 0,
        ]);

        $video = Photo::create([
            'title' => 'Promo Loop',
            'file_path' => 'videos/promo.mp4',
            'type' => 'video',
            'is_active' => true,
        ]);

        PhotoGroupItem::create([
            'photo_group_id' => $group->id,
            'photo_id' => $video->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->get("/display/{$group->id}");

        $response->assertOk();
        $response->assertDontSee('Klik untuk aktifkan suara', false);
        $response->assertDontSee('unmute-hint', false);
        $response->assertSee('video.muted = false', false);
    }

    public function test_group_media_management_uses_media_label_for_mixed_items(): void
    {
        $group = PhotoGroup::create([
            'name' => 'Lobby Display',
            'is_active' => true,
            'slide_duration' => 5,
            'sort_order' => 0,
        ]);

        $photo = Photo::create([
            'title' => 'Welcome Photo',
            'file_path' => 'photos/welcome.jpg',
            'type' => 'photo',
            'is_active' => true,
        ]);

        PhotoGroupItem::create([
            'photo_group_id' => $group->id,
            'photo_id' => $photo->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin())->get("/photo-groups/{$group->id}/items");

        $response->assertOk();
        $response->assertSee('Tambah Media ke Grup', false);
        $response->assertDontSee('Tambah Foto', false);
        $response->assertDontSee('Hapus foto ini dari grup?', false);
    }

    public function test_photo_edit_page_renders_framing_preview(): void
    {
        $photo = Photo::create([
            'title' => 'Framing',
            'file_path' => 'photos/framing.jpg',
            'type' => 'photo',
            'is_active' => true,
            'focus_x' => 40,
            'focus_y' => 60,
            'crop_zoom' => 150,
            'crop_data' => [
                'main' => ['fx' => 40, 'fy' => 60, 'zoom' => 150],
                'facilities' => ['fx' => 25, 'fy' => 75, 'zoom' => 200],
                'next_event' => ['fx' => 55, 'fy' => 45, 'zoom' => 300],
            ],
        ]);

        $response = $this->actingAs($this->admin())->get("/photos/{$photo->id}/edit");

        $response->assertOk();
        $response->assertSee('crop-box', false);
        $response->assertSee('crop-wrap', false);
        $response->assertSee('activateSlot', false);
        $response->assertSee('fitBox', false);
        $response->assertSee('zoom-range', false);
        $response->assertSee('framing-label', false);
        $response->assertSee('draft-badge', false);
        $response->assertSee('localStorage', false);
        $response->assertSee('name="crop_data[main][fx]"', false);
        $response->assertSee('name="crop_data[facilities][fx]"', false);
        $response->assertSee('name="crop_data[next_event][fx]"', false);
        $response->assertSee('value="200"', false);
        $response->assertSee('value="300"', false);
    }

    public function test_photo_update_persists_per_slot_framing(): void
    {
        $photo = Photo::create([
            'title' => 'Before',
            'file_path' => 'photos/focus.jpg',
            'type' => 'photo',
            'is_active' => true,
            'focus_x' => 50,
            'focus_y' => 50,
            'crop_zoom' => 100,
        ]);

        $response = $this->actingAs($this->admin())->put("/photos/{$photo->id}", [
            'title' => 'After',
            'is_active' => '1',
            'crop_data' => [
                'main' => ['fx' => 35, 'fy' => 65, 'zoom' => 250],
                'facilities' => ['fx' => 20, 'fy' => 80, 'zoom' => 300],
                'next_event' => ['fx' => 60, 'fy' => 40, 'zoom' => 150],
            ],
        ]);

        $response->assertRedirect(route('photos.index'));

        $photo->refresh();
        $this->assertSame('After', $photo->title);
        // Legacy single framing stays untouched once per-slot data exists;
        // the display fully consumes crop_data from that point on.
        $this->assertSame(50, $photo->focus_x);
        $this->assertSame(50, $photo->focus_y);
        $this->assertSame(100, $photo->crop_zoom);
        $this->assertSame([
            'main' => ['fx' => 35, 'fy' => 65, 'zoom' => 250],
            'facilities' => ['fx' => 20, 'fy' => 80, 'zoom' => 300],
            'next_event' => ['fx' => 60, 'fy' => 40, 'zoom' => 150],
        ], $photo->crop_data);
    }

    public function test_photo_update_fills_missing_slots_from_legacy_framing(): void
    {
        $photo = Photo::create([
            'title' => 'Partial',
            'file_path' => 'photos/partial.jpg',
            'type' => 'photo',
            'is_active' => true,
            'focus_x' => 45,
            'focus_y' => 55,
            'crop_zoom' => 180,
        ]);

        $response = $this->actingAs($this->admin())->put("/photos/{$photo->id}", [
            'title' => 'Partial',
            'is_active' => '1',
            'crop_data' => [
                'main' => ['fx' => 10, 'fy' => 90, 'zoom' => 220],
            ],
        ]);

        $response->assertRedirect(route('photos.index'));

        $photo->refresh();
        $this->assertSame(220, $photo->crop_data['main']['zoom']);
        $this->assertSame(10, $photo->crop_data['main']['fx']);
        $this->assertSame(180, $photo->crop_data['facilities']['zoom']);
        $this->assertSame(45, $photo->crop_data['facilities']['fx']);
        $this->assertSame(55, $photo->crop_data['facilities']['fy']);
        $this->assertSame(45, $photo->crop_data['next_event']['fx']);
        $this->assertSame(55, $photo->crop_data['next_event']['fy']);
    }
}
