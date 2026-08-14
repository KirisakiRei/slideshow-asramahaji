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
}
