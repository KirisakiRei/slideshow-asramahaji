<?php

namespace App\Http\Controllers;

use App\Models\DisplaySlotGroup;
use App\Models\Facility;
use App\Models\PhotoGroup;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SignageController extends Controller
{
    private const GROUP_IDS_RULE = ['array'];

    /**
     * Header & title settings page.
     */
    public function header()
    {
        return view('admin.signage.header', ['config' => Setting::displayConfig()]);
    }

    public function updateHeader(Request $request)
    {
        $request->validate([
            'eyebrow' => ['nullable', 'string', 'max:100'],
            'title' => ['nullable', 'string', 'max:255'],
            'logo_text' => ['nullable', 'string', 'max:200'],
            'logo' => ['nullable', 'file', 'mimes:jpeg,jpg,png,gif,webp,svg', 'max:5120'],
        ], [
            'logo.mimes' => 'Logo harus berformat: JPEG, PNG, GIF, WEBP, atau SVG.',
            'logo.max' => 'Ukuran logo tidak boleh melebihi 5MB.',
        ]);

        if ($request->hasFile('logo')) {
            $old = Setting::get('header_logo');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            $path = $request->file('logo')->storeAs(
                'settings',
                'logo_' . uniqid() . '.' . $request->file('logo')->getClientOriginalExtension(),
                'public'
            );
            Setting::set('header_logo', $path);
        }

        Setting::set('display_eyebrow', $request->input('eyebrow', ''));
        Setting::set('display_title', $request->input('title', ''));
        Setting::set('header_logo_text', $request->input('logo_text', ''));

        return redirect()->route('signage.header')->with('success', 'Header display berhasil disimpan.');
    }

    public function removeLogo()
    {
        $logo = Setting::get('header_logo');
        if ($logo && Storage::disk('public')->exists($logo)) {
            Storage::disk('public')->delete($logo);
        }
        Setting::set('header_logo', null);

        return redirect()->route('signage.header')->with('success', 'Logo berhasil dihapus.');
    }

    /**
     * Main slideshow slot page.
     */
    public function main()
    {
        $config = Setting::displayConfig();
        $groups = PhotoGroup::orderBy('sort_order')->orderBy('name')->get();
        $groupPreviews = $this->groupPreviews($groups);
        $placements = DisplaySlotGroup::orderedFor(DisplaySlotGroup::SLOT_MAIN);

        return view('admin.signage.main', compact('config', 'groups', 'groupPreviews', 'placements'));
    }

    public function updateMain(Request $request)
    {
        $request->validate([
            'group_ids' => [self::GROUP_IDS_RULE],
            'group_ids.*' => ['integer', 'exists:photo_groups,id'],
        ]);

        DisplaySlotGroup::sync(DisplaySlotGroup::SLOT_MAIN, $request->input('group_ids', []));

        return redirect()->route('signage.main')->with('success', 'Slideshow utama berhasil disimpan.');
    }

    /**
     * Facilities page (3 fixed slots, each with its own group list).
     */
    public function facilities()
    {
        $config = Setting::displayConfig();
        $groups = PhotoGroup::orderBy('sort_order')->orderBy('name')->get();
        $groupPreviews = $this->groupPreviews($groups);
        $facilities = Facility::orderBy('slot')->get()->keyBy('slot');
        $placements = [];
        foreach ([1, 2, 3] as $slot) {
            $placements[$slot] = DisplaySlotGroup::orderedFor("facility_{$slot}");
        }

        return view('admin.signage.facilities', compact('config', 'groups', 'groupPreviews', 'facilities', 'placements'));
    }

    public function updateFacilities(Request $request)
    {
        $request->validate([
            'section_chip' => ['nullable', 'string', 'max:100'],
            'show_facility_captions' => ['nullable', 'boolean'],
            'caption_1' => ['nullable', 'string', 'max:100'],
            'caption_2' => ['nullable', 'string', 'max:100'],
            'caption_3' => ['nullable', 'string', 'max:100'],
            'group_ids_1' => [self::GROUP_IDS_RULE],
            'group_ids_1.*' => ['integer', 'exists:photo_groups,id'],
            'group_ids_2' => [self::GROUP_IDS_RULE],
            'group_ids_2.*' => ['integer', 'exists:photo_groups,id'],
            'group_ids_3' => [self::GROUP_IDS_RULE],
            'group_ids_3.*' => ['integer', 'exists:photo_groups,id'],
        ]);

        Setting::set('display_section_chip', $request->input('section_chip', ''));
        Setting::set(
            'display_show_facility_captions',
            $request->boolean('show_facility_captions') ? '1' : '0'
        );

        foreach ([1, 2, 3] as $slot) {
            Facility::updateOrCreate(
                ['slot' => $slot],
                ['caption' => $request->input("caption_{$slot}", '')]
            );
            DisplaySlotGroup::sync(
                "facility_{$slot}",
                $request->input("group_ids_{$slot}", [])
            );
        }

        return redirect()->route('signage.facilities')->with('success', 'Fasilitas berhasil disimpan.');
    }

    /**
     * Next event page.
     */
    public function nextEvent()
    {
        $config = Setting::displayConfig();
        $groups = PhotoGroup::orderBy('sort_order')->orderBy('name')->get();
        $groupPreviews = $this->groupPreviews($groups);
        $placements = DisplaySlotGroup::orderedFor(DisplaySlotGroup::SLOT_NEXT_EVENT);

        return view('admin.signage.next-event', compact('config', 'groups', 'groupPreviews', 'placements'));
    }

    public function updateNextEvent(Request $request)
    {
        $request->validate([
            'next_event_label' => ['nullable', 'string', 'max:100'],
            'next_event_title' => ['nullable', 'string', 'max:255'],
            'next_event_organizer' => ['nullable', 'string', 'max:255'],
            'next_event_date' => ['nullable', 'string', 'max:100'],
            'next_event_time' => ['nullable', 'string', 'max:50'],
            'next_event_location' => ['nullable', 'string', 'max:255'],
            'next_event_category' => ['nullable', 'string', 'max:100'],
            'group_ids' => [self::GROUP_IDS_RULE],
            'group_ids.*' => ['integer', 'exists:photo_groups,id'],
        ]);

        foreach ([
            'next_event_label',
            'next_event_title',
            'next_event_organizer',
            'next_event_date',
            'next_event_time',
            'next_event_location',
            'next_event_category',
        ] as $key) {
            Setting::set($key, $request->input($key, ''));
        }

        DisplaySlotGroup::sync(DisplaySlotGroup::SLOT_NEXT_EVENT, $request->input('group_ids', []));

        return redirect()->route('signage.next-event')->with('success', 'Event selanjutnya berhasil disimpan.');
    }

    /**
     * Footer info page.
     */
    public function footer()
    {
        return view('admin.signage.footer', ['config' => Setting::displayConfig()]);
    }

    public function updateFooter(Request $request)
    {
        $request->validate([
            'footer_title' => ['nullable', 'string', 'max:255'],
            'footer_subtitle' => ['nullable', 'string', 'max:255'],
            'footer_support' => ['nullable', 'string', 'max:500'],
            'clock_offset' => ['nullable', 'integer', 'min:-3600', 'max:3600'],
        ]);

        Setting::set('footer_title', $request->input('footer_title', ''));
        Setting::set('footer_subtitle', $request->input('footer_subtitle', ''));
        Setting::set('footer_support', $request->input('footer_support', ''));
        Setting::set('clock_offset', (int) $request->input('clock_offset', 0));

        return redirect()->route('signage.footer')->with('success', 'Info footer dan pengaturan jam berhasil disimpan.');
    }

    /**
     * First active media thumbnail per group, keyed by group id.
     */
    private function groupPreviews($groups): array
    {
        $previews = [];

        foreach ($groups as $group) {
            $item = $group->activePhotos()->with('photo')->first();
            $previews[$group->id] = $item && $item->photo ? [
                'url' => asset('storage/' . $item->photo->file_path),
                'type' => $item->photo->type ?? 'photo',
            ] : null;
        }

        return $previews;
    }
}
