<?php

namespace Tests\Feature;

use App\Http\Controllers\DisplayController;
use App\Models\DisplaySlotGroup;
use App\Models\Photo;
use App\Models\PhotoGroup;
use App\Models\PhotoGroupItem;
use Database\Seeders\FacilitySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisplayFillModeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(FacilitySeeder::class);
    }

    private function groupWithPhoto(string $name, string $fillMode = 'cover', int $focusX = 50, int $focusY = 50, int $zoom = 100): PhotoGroup
    {
        $group = PhotoGroup::create([
            'name' => $name,
            'is_active' => true,
            'slide_duration' => 4,
            'sort_order' => 0,
            'fill_mode' => $fillMode,
        ]);

        $photo = Photo::create([
            'title' => $name.' photo',
            'file_path' => 'photos/'.strtolower(str_replace(' ', '-', $name)).'.jpg',
            'type' => 'photo',
            'is_active' => true,
            'focus_x' => $focusX,
            'focus_y' => $focusY,
            'crop_zoom' => $zoom,
        ]);

        PhotoGroupItem::create([
            'photo_group_id' => $group->id,
            'photo_id' => $photo->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        return $group;
    }

    public function test_slides_carry_fill_mode_and_focus_point(): void
    {
        $group = $this->groupWithPhoto('Grup', 'contain', 30, 70);

        $response = $this->get("/display/{$group->id}");

        $response->assertOk();
        $slide = $response->viewData('slides')[0];
        $this->assertSame('contain', $slide['fill']);
        $this->assertSame(30, $slide['focusX']);
        $this->assertSame(70, $slide['focusY']);
    }

    public function test_defaults_are_cover_and_center(): void
    {
        $group = $this->groupWithPhoto('Grup Default');

        $response = $this->get("/display/{$group->id}");

        $response->assertOk();
        $slide = $response->viewData('slides')[0];
        $this->assertSame('cover', $slide['fill']);
        $this->assertSame(50, $slide['focusX']);
        $this->assertSame(50, $slide['focusY']);
    }

    public function test_default_zoom_is_100(): void
    {
        $group = $this->groupWithPhoto('Grup Zoom Default');

        $response = $this->get("/display/{$group->id}");

        $response->assertOk();
        $slide = $response->viewData('slides')[0];
        $this->assertSame(100, $slide['zoom']);
    }

    public function test_slides_carry_crop_zoom(): void
    {
        $group = $this->groupWithPhoto('Grup Zoom', 'cover', 20, 80, 250);

        $response = $this->get("/display/{$group->id}");

        $response->assertOk();
        $slide = $response->viewData('slides')[0];
        $this->assertSame(250, $slide['zoom']);
    }

    public function test_zoom_is_clamped_between_100_and_400(): void
    {
        $low = $this->groupWithPhoto('Grup Zoom Rendah', 'cover', 50, 50, 30);
        $high = $this->groupWithPhoto('Grup Zoom Tinggi', 'cover', 50, 50, 900);

        $lowSlide = $this->get("/display/{$low->id}")->assertOk()->viewData('slides')[0];
        $highSlide = $this->get("/display/{$high->id}")->assertOk()->viewData('slides')[0];

        $this->assertSame(100, $lowSlide['zoom']);
        $this->assertSame(400, $highSlide['zoom']);
    }

    public function test_fill_mode_change_invalidates_status_hash(): void
    {
        $group = $this->groupWithPhoto('Grup Hash');

        $h1 = $this->get('/display/status')->assertOk()->json('hash');

        $group->update(['fill_mode' => 'contain']);
        DisplayController::forgetStatusHashCache();

        $h2 = $this->get('/display/status')->assertOk()->json('hash');

        $this->assertNotSame($h1, $h2);
    }

    public function test_focus_point_change_invalidates_status_hash(): void
    {
        $this->groupWithPhoto('Grup Hash Fokus');

        $h1 = $this->get('/display/status')->assertOk()->json('hash');

        Photo::query()->update(['focus_x' => 10, 'focus_y' => 90]);
        DisplayController::forgetStatusHashCache();

        $h2 = $this->get('/display/status')->assertOk()->json('hash');

        $this->assertNotSame($h1, $h2);
    }

    public function test_crop_zoom_change_invalidates_status_hash(): void
    {
        $this->groupWithPhoto('Grup Hash Zoom');

        $h1 = $this->get('/display/status')->assertOk()->json('hash');

        Photo::query()->update(['crop_zoom' => 180]);
        DisplayController::forgetStatusHashCache();

        $h2 = $this->get('/display/status')->assertOk()->json('hash');

        $this->assertNotSame($h1, $h2);
    }

    public function test_slots_use_their_own_framing_data(): void
    {
        $group = PhotoGroup::create([
            'name' => 'Grup Multi Framing',
            'is_active' => true,
            'slide_duration' => 4,
            'sort_order' => 0,
            'fill_mode' => 'cover',
        ]);

        $photo = Photo::create([
            'title' => 'Multi Frame',
            'file_path' => 'photos/multi.jpg',
            'type' => 'photo',
            'is_active' => true,
            'focus_x' => 50,
            'focus_y' => 50,
            'crop_zoom' => 100,
            'crop_data' => [
                'main' => ['fx' => 10, 'fy' => 20, 'zoom' => 150],
                'facilities' => ['fx' => 30, 'fy' => 40, 'zoom' => 200],
                'next_event' => ['fx' => 70, 'fy' => 80, 'zoom' => 110],
            ],
        ]);

        PhotoGroupItem::create([
            'photo_group_id' => $group->id,
            'photo_id' => $photo->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        DisplaySlotGroup::sync(DisplaySlotGroup::SLOT_MAIN, [$group->id]);
        DisplaySlotGroup::sync(DisplaySlotGroup::SLOT_FACILITY_1, [$group->id]);
        DisplaySlotGroup::sync(DisplaySlotGroup::SLOT_NEXT_EVENT, [$group->id]);

        $response = $this->get('/display')->assertOk();

        $main = $response->viewData('slides')[0];
        $this->assertSame(150, $main['zoom']);
        $this->assertSame(10, $main['focusX']);
        $this->assertSame(20, $main['focusY']);

        $facilitySlot = collect($response->viewData('facilitySlots'))
            ->first(fn ($s) => ! empty($s['slides']));
        $this->assertNotNull($facilitySlot, 'No facility slot rendered');
        $this->assertSame(200, $facilitySlot['slides'][0]['zoom']);
        $this->assertSame(30, $facilitySlot['slides'][0]['focusX']);
        $this->assertSame(40, $facilitySlot['slides'][0]['focusY']);

        $event = $response->viewData('eventSlides')[0];
        $this->assertSame(110, $event['zoom']);
        $this->assertSame(70, $event['focusX']);
        $this->assertSame(80, $event['focusY']);
    }

    public function test_slots_fall_back_to_legacy_framing_without_crop_data(): void
    {
        $group = $this->groupWithPhoto('Grup Legacy', 'cover', 20, 80, 250);

        DisplaySlotGroup::sync(DisplaySlotGroup::SLOT_MAIN, [$group->id]);
        DisplaySlotGroup::sync(DisplaySlotGroup::SLOT_FACILITY_2, [$group->id]);
        DisplaySlotGroup::sync(DisplaySlotGroup::SLOT_NEXT_EVENT, [$group->id]);

        $response = $this->get('/display')->assertOk();

        $main = $response->viewData('slides')[0];
        $this->assertSame(250, $main['zoom']);
        $this->assertSame(20, $main['focusX']);
        $this->assertSame(80, $main['focusY']);

        $facilitySlot = collect($response->viewData('facilitySlots'))
            ->first(fn ($s) => ! empty($s['slides']));
        $this->assertNotNull($facilitySlot, 'No facility slot rendered');
        $this->assertSame(250, $facilitySlot['slides'][0]['zoom']);
        $this->assertSame(20, $facilitySlot['slides'][0]['focusX']);

        $event = $response->viewData('eventSlides')[0];
        $this->assertSame(250, $event['zoom']);
        $this->assertSame(80, $event['focusY']);
    }

    public function test_crop_data_change_invalidates_status_hash(): void
    {
        $this->groupWithPhoto('Grup Hash Crop Data');

        $h1 = $this->get('/display/status')->assertOk()->json('hash');

        Photo::query()->update([
            'crop_data' => ['main' => ['fx' => 12, 'fy' => 34, 'zoom' => 160]],
        ]);
        DisplayController::forgetStatusHashCache();

        $h2 = $this->get('/display/status')->assertOk()->json('hash');

        $this->assertNotSame($h1, $h2);
    }

    public function test_group_preview_applies_slot_framing_per_field(): void
    {
        $group = PhotoGroup::create([
            'name' => 'Grup Preview',
            'is_active' => true,
            'slide_duration' => 4,
            'sort_order' => 0,
            'fill_mode' => 'cover',
        ]);

        $photo = Photo::create([
            'title' => 'Preview Frame',
            'file_path' => 'photos/preview.jpg',
            'type' => 'photo',
            'is_active' => true,
            'focus_x' => 50,
            'focus_y' => 50,
            'crop_zoom' => 100,
            'crop_data' => [
                'main' => ['fx' => 15, 'fy' => 25, 'zoom' => 140],
                'facilities' => ['fx' => 35, 'fy' => 45, 'zoom' => 210],
                'next_event' => ['fx' => 65, 'fy' => 75, 'zoom' => 120],
            ],
        ]);

        PhotoGroupItem::create([
            'photo_group_id' => $group->id,
            'photo_id' => $photo->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->get("/display/{$group->id}")->assertOk();

        $this->assertSame(140, $response->viewData('slides')[0]['zoom']);

        $facilitySlot = collect($response->viewData('facilitySlots'))
            ->first(fn ($s) => ! empty($s['slides']));
        $this->assertNotNull($facilitySlot, 'No facility slot rendered');
        $this->assertSame(210, $facilitySlot['slides'][0]['zoom']);

        $this->assertSame(120, $response->viewData('eventSlides')[0]['zoom']);
    }
}
