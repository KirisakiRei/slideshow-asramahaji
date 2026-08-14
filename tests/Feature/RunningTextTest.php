<?php

namespace Tests\Feature;

use App\Models\RunningText;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RunningTextTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
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

    public function test_store_and_display_running_text(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/running-texts', [
            'text' => 'Selamat datang',
        ])->assertRedirect(route('running-texts.index'));

        $this->assertDatabaseHas('running_texts', [
            'text' => 'Selamat datang',
            'is_active' => true,
        ]);

        $response = $this->get('/display');

        $response->assertOk();
        $this->assertCount(1, $response->viewData('runningTexts'));
        $this->assertSame('Selamat datang', $response->viewData('runningTexts')[0]->text);
        $response->assertSee('id="marquee"', false);
    }

    public function test_inactive_running_text_is_not_shown(): void
    {
        $admin = $this->admin();
        $text = RunningText::create([
            'text' => 'Tersembunyi',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->actingAs($admin)->post("/running-texts/{$text->id}/toggle");

        $response = $this->get('/display');

        $response->assertOk();
        $this->assertCount(0, $response->viewData('runningTexts'));
        $response->assertDontSee('id="marquee"', false);
    }

    public function test_running_texts_ordered_by_sort_order(): void
    {
        RunningText::create(['text' => 'Teks A', 'is_active' => true, 'sort_order' => 1]);
        RunningText::create(['text' => 'Teks B', 'is_active' => true, 'sort_order' => 2]);

        $response = $this->get('/display');

        $response->assertOk();
        $this->assertSame(['Teks A', 'Teks B'], $response->viewData('runningTexts')->pluck('text')->all());
    }
}
