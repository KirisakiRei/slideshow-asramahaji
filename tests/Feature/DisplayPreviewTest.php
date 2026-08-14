<?php

namespace Tests\Feature;

use App\Models\Photo;
use App\Models\PhotoGroup;
use App\Models\PhotoGroupItem;
use App\Models\User;
use Database\Seeders\FacilitySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisplayPreviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(FacilitySeeder::class);
    }

    private function groupWithPhoto(string $name, bool $active = true, int $photos = 1): PhotoGroup
    {
        $group = PhotoGroup::create([
            'name' => $name,
            'is_active' => $active,
            'slide_duration' => 4,
            'sort_order' => 0,
        ]);

        for ($i = 1; $i <= $photos; $i++) {
            $photo = Photo::create([
                'title' => $name.' photo '.$i,
                'file_path' => 'photos/'.strtolower(str_replace(' ', '-', $name))."-{$i}.jpg",
                'type' => 'photo',
                'is_active' => true,
            ]);

            PhotoGroupItem::create([
                'photo_group_id' => $group->id,
                'photo_id' => $photo->id,
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }

        return $group;
    }

    public function test_preview_group_populates_every_slideshow_field(): void
    {
        $group = $this->groupWithPhoto('Preview Grup', true, 2);

        $response = $this->get("/display/{$group->id}");

        $response->assertOk();
        $response->assertViewHas('previewGroupName', 'Preview Grup');

        $slides = $response->viewData('slides');
        $this->assertCount(2, $slides);

        $facilitySlots = $response->viewData('facilitySlots');
        $this->assertNotEmpty($facilitySlots);
        foreach ($facilitySlots as $slotData) {
            $this->assertSame($slides, $slotData['slides']);
        }

        $this->assertSame($slides, $response->viewData('eventSlides'));
    }

    public function test_preview_page_shows_badge_with_group_name(): void
    {
        $group = $this->groupWithPhoto('Badge Grup');

        $this->get("/display/{$group->id}")->assertSee('Preview: Badge Grup');
    }

    public function test_missing_group_returns_404(): void
    {
        $this->get('/display/99999')->assertNotFound();
    }

    public function test_inactive_group_returns_404(): void
    {
        $group = $this->groupWithPhoto('Nonaktif', false);

        $this->get("/display/{$group->id}")->assertNotFound();
    }

    public function test_group_without_active_media_returns_404(): void
    {
        $group = PhotoGroup::create([
            'name' => 'Kosong',
            'is_active' => true,
            'slide_duration' => 4,
            'sort_order' => 0,
        ]);

        $this->get("/display/{$group->id}")->assertNotFound();
    }

    public function test_main_display_still_uses_slots(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $mainGroup = $this->groupWithPhoto('Main Grup');
        $this->groupWithPhoto('Preview Grup');

        $this->actingAs($admin)->post('/signage/main', [
            'group_ids' => [$mainGroup->id],
        ])->assertRedirect();

        $response = $this->get('/display');

        $response->assertOk();
        $this->assertSame('Main Grup', $response->viewData('slides')[0]['group']);
        $this->assertNull($response->viewData('previewGroupName'));
    }
}
