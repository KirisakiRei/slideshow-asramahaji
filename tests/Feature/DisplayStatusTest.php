<?php

namespace Tests\Feature;

use App\Http\Controllers\DisplayController;
use App\Models\RunningText;
use Database\Seeders\FacilitySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisplayStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(FacilitySeeder::class);
    }

    public function test_status_hash_is_stable_until_content_changes(): void
    {
        $h1 = $this->get('/display/status')->assertOk()->json('hash');
        $h2 = $this->get('/display/status')->assertOk()->json('hash');
        $this->assertSame($h1, $h2);

        RunningText::create(['text' => 'Teks baru', 'is_active' => true, 'sort_order' => 1]);

        // The hash is cached for a few seconds by design; invalidate it so the
        // next poll reflects the content change.
        DisplayController::forgetStatusHashCache();

        $h3 = $this->get('/display/status')->assertOk()->json('hash');
        $this->assertNotSame($h1, $h3);
    }

    public function test_display_page_embeds_the_same_hash(): void
    {
        $response = $this->get('/display');

        $response->assertOk();
        $this->assertSame(
            $this->get('/display/status')->json('hash'),
            $response->viewData('statusHash')
        );
    }
}
