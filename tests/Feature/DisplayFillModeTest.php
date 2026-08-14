<?php

namespace Tests\Feature;

use App\Http\Controllers\DisplayController;
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

    private function groupWithPhoto(string $name, string $fillMode = 'cover', int $focusX = 50, int $focusY = 50): PhotoGroup
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
}
