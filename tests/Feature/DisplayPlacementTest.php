<?php

namespace Tests\Feature;

use Database\Seeders\FacilitySeeder;
use App\Models\Photo;
use App\Models\PhotoGroup;
use App\Models\PhotoGroupItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisplayPlacementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(FacilitySeeder::class);
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

    private function groupWithPhoto(string $name): PhotoGroup
    {
        $group = PhotoGroup::create([
            'name' => $name,
            'is_active' => true,
            'slide_duration' => 4,
            'sort_order' => 0,
        ]);

        $photo = Photo::create([
            'title' => $name . ' photo',
            'file_path' => 'photos/' . strtolower(str_replace(' ', '-', $name)) . '.jpg',
            'type' => 'photo',
            'is_active' => true,
        ]);

        PhotoGroupItem::create([
            'photo_group_id' => $group->id,
            'photo_id' => $photo->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        return $group;
    }

    public function test_main_slot_plays_selected_groups_in_order(): void
    {
        $admin = $this->admin();
        $groupA = $this->groupWithPhoto('Grup A');
        $groupB = $this->groupWithPhoto('Grup B');

        $this->actingAs($admin)->post('/signage/main', [
            'group_ids' => [$groupB->id, $groupA->id],
        ])->assertRedirect(route('signage.main'));

        $response = $this->get('/display');

        $response->assertOk();
        $response->assertViewHas('error', null);

        $slides = $response->viewData('slides');
        $this->assertCount(2, $slides);
        $this->assertSame('Grup B', $slides[0]['group']);
        $this->assertSame('Grup A', $slides[1]['group']);
    }

    public function test_main_slot_without_groups_shows_message(): void
    {
        $response = $this->get('/display');

        $response->assertOk();
        $response->assertViewHas('error', 'Belum ada grup dipilih untuk slideshow utama');
    }

    public function test_facility_slot_slides_are_passed_to_view(): void
    {
        $admin = $this->admin();
        $group = $this->groupWithPhoto('Fasilitas Grup');

        $this->actingAs($admin)->post('/signage/facilities', [
            'section_chip' => 'Fasilitas',
            'caption_1' => 'Aula Utama',
            'group_ids_1' => [$group->id],
            'caption_2' => '',
            'caption_3' => '',
        ])->assertRedirect(route('signage.facilities'));

        $response = $this->get('/display');

        $response->assertOk();
        $facilitySlots = $response->viewData('facilitySlots');
        $this->assertCount(3, $facilitySlots);
        $this->assertCount(1, $facilitySlots[1]['slides']);
        $this->assertSame('Aula Utama', $facilitySlots[1]['facility']->caption);
        $this->assertSame('Fasilitas Grup', $facilitySlots[1]['slides'][0]['group']);
    }

    public function test_next_event_slot_slides_are_passed_to_view(): void
    {
        $admin = $this->admin();
        $group = $this->groupWithPhoto('Event Grup');

        $this->actingAs($admin)->post('/signage/next-event', [
            'next_event_label' => 'Event Selanjutnya',
            'next_event_title' => 'Workshop',
            'group_ids' => [$group->id],
        ])->assertRedirect(route('signage.next-event'));

        $response = $this->get('/display');

        $response->assertOk();
        $eventSlides = $response->viewData('eventSlides');
        $this->assertCount(1, $eventSlides);
        $this->assertSame('Event Grup', $eventSlides[0]['group']);
    }

    public function test_facility_and_event_slides_carry_group_effect_config(): void
    {
        $admin = $this->admin();
        $group = $this->groupWithPhoto('Grup Efek');
        $group->update(['transition_type' => 'slide-up', 'slide_duration' => 7]);

        $this->actingAs($admin)->post('/signage/facilities', [
            'section_chip' => 'Fasilitas',
            'caption_1' => 'Aula Utama',
            'group_ids_1' => [$group->id],
            'caption_2' => '',
            'caption_3' => '',
        ])->assertRedirect(route('signage.facilities'));

        $this->actingAs($admin)->post('/signage/next-event', [
            'next_event_label' => 'Event Selanjutnya',
            'next_event_title' => 'Workshop',
            'group_ids' => [$group->id],
        ])->assertRedirect(route('signage.next-event'));

        $response = $this->get('/display');

        $response->assertOk();
        $facilitySlots = $response->viewData('facilitySlots');
        $eventSlides = $response->viewData('eventSlides');

        $this->assertSame('slide-up', $facilitySlots[1]['slides'][0]['transition']);
        $this->assertSame(7, $facilitySlots[1]['slides'][0]['duration']);
        $this->assertSame('slide-up', $eventSlides[0]['transition']);
        $this->assertSame(7, $eventSlides[0]['duration']);
    }
}
