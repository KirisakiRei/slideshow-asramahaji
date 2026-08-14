<?php

namespace Tests\Feature;

use App\Http\Controllers\DisplayController;
use Database\Seeders\FacilitySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Tests\TestCase;

class DisplayResilienceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(FacilitySeeder::class);
    }

    public function test_status_returns_stable_sentinel_when_hash_computation_fails(): void
    {
        Cache::shouldReceive('remember')
            ->andThrow(new RuntimeException('cache down'));

        $response = $this->get('/display/status');

        $response->assertOk();
        $this->assertSame(DisplayController::HASH_UNAVAILABLE, $response->json('hash'));
    }

    public function test_page_uses_stable_fallback_hash_when_hash_computation_fails(): void
    {
        Cache::shouldReceive('rememberForever')->andReturn([]);
        Cache::shouldReceive('remember')
            ->andThrow(new RuntimeException('cache down'));

        $response = $this->get('/display');

        $response->assertOk();
        $this->assertSame(DisplayController::HASH_FALLBACK, $response->viewData('statusHash'));
    }

    public function test_global_handler_serves_fallback_view_for_display_errors(): void
    {
        $this->mock(DisplayController::class, function ($mock) {
            $mock->shouldReceive('show')->andThrow(new RuntimeException('boom'));
        });

        $response = $this->get('/display/999');

        $response->assertOk();
        $response->assertSee('Display sementara tidak tersedia');
        $this->assertSame(DisplayController::HASH_ERROR, $response->viewData('statusHash'));
    }

    public function test_global_handler_serves_stable_json_error_for_json_requests(): void
    {
        $this->mock(DisplayController::class, function ($mock) {
            $mock->shouldReceive('show')->andThrow(new RuntimeException('boom'));
        });

        $response = $this->getJson('/display/999');

        $response->assertOk();
        $response->assertJson([
            'hash' => DisplayController::HASH_ERROR,
            'message' => 'Display status unavailable',
        ]);
    }
}
