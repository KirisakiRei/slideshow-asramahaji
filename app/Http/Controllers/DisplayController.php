<?php

namespace App\Http\Controllers;

use App\Models\DisplaySlotGroup;
use App\Models\Facility;
use App\Models\Photo;
use App\Models\PhotoGroup;
use App\Models\RunningText;
use App\Models\Setting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class DisplayController extends Controller
{
    /**
     * Short TTL for status polling. Signage does not need sub-second freshness,
     * and this avoids hammering the database every few seconds per kiosk.
     */
    private const STATUS_HASH_CACHE_SECONDS = 3;

    /**
     * Stable sentinel hashes (never include timestamps).
     * Time-based hashes cause kiosk thrash-reloads.
     */
    public const HASH_UNAVAILABLE = 'unavailable';

    public const HASH_FALLBACK = 'fallback';

    public const HASH_ERROR = 'error';

    /**
     * Main display: play the groups assigned to the main slot, in slot order.
     */
    public function index()
    {
        return $this->renderDisplay(function () {
            $mainSlides = $this->slotSlides(DisplaySlotGroup::SLOT_MAIN);
            $mainEmpty = DisplaySlotGroup::orderedFor(DisplaySlotGroup::SLOT_MAIN)->isEmpty();

            $error = $mainEmpty
                ? 'Belum ada grup dipilih untuk slideshow utama'
                : (empty($mainSlides) ? 'Tidak ada media aktif untuk ditampilkan' : null);

            return [
                'slides' => $mainSlides,
                'error' => $error,
            ];
        });
    }

    /**
     * Single group preview: /display/{id} shows the group's media in every
     * slideshow field (hero, facilities, next event) so admins can preview a
     * group before assigning it to a live slot.
     */
    public function show($group)
    {
        return $this->renderDisplay(fn ($previewSlides) => [
            'slides' => $previewSlides,
            'error' => null,
        ], $group);
    }

    /**
     * Content fingerprint for auto-refresh polling on signage displays.
     */
    public function status()
    {
        try {
            return response()->json(['hash' => $this->contentHash()]);
        } catch (Throwable $e) {
            Log::error('Display status hash failed', [
                'message' => $e->getMessage(),
            ]);

            // Stable fallback so clients do not thrash-reload on transient errors.
            return response()->json(['hash' => self::HASH_UNAVAILABLE], 200);
        }
    }

    /**
     * Build the display page with safe defaults when a subsystem fails.
     *
     * When $previewGroupId is given, that group's media replaces the content
     * of every slideshow field (preview mode for /display/{id}).
     */
    private function renderDisplay(callable $buildMain, $previewGroupId = null): View|Response
    {
        $previewGroup = $previewGroupId !== null ? PhotoGroup::find($previewGroupId) : null;
        $previewSlides = [];
        $previewGroupName = null;

        // Preview contract: a group that is missing, inactive, or without
        // active media is treated as not found.
        if ($previewGroupId !== null && (! $previewGroup || ! $previewGroup->is_active)) {
            return $this->errorView('Slideshow tidak tersedia', 404);
        }

        if ($previewGroup !== null) {
            $previewSlides = $this->safeGroupSlides($previewGroup, 'main');

            if (empty($previewSlides)) {
                return $this->errorView('Slideshow tidak tersedia', 404);
            }

            $previewGroupName = $previewGroup->name;
        }

        $config = $this->safeConfig();
        $facilities = $this->safeFacilities();
        $facilitySlots = $previewGroup !== null
            ? $this->buildFacilitySlots($facilities, fn () => $this->safeGroupSlides($previewGroup, 'facilities'))
            : $this->buildFacilitySlots($facilities, fn ($slot) => $this->safeSlotSlides("facility_{$slot}"));
        $eventSlides = $previewGroup !== null
            ? $this->safeGroupSlides($previewGroup, 'next_event')
            : $this->safeSlotSlides(DisplaySlotGroup::SLOT_NEXT_EVENT);
        $runningTexts = $this->safeRunningTexts();
        $statusHash = $this->safeContentHash();

        try {
            $main = $buildMain($previewSlides);
            $slides = is_array($main['slides'] ?? null) ? $main['slides'] : [];
            $error = $main['error'] ?? null;
        } catch (Throwable $e) {
            Log::error('Display main playlist failed', [
                'message' => $e->getMessage(),
            ]);
            $slides = [];
            $error = 'Display sementara tidak tersedia. Mencoba memuat ulang…';
        }

        return view('display.show', [
            'slides' => $slides,
            'config' => $config,
            'facilitySlots' => $facilitySlots,
            'eventSlides' => $eventSlides,
            'runningTexts' => $runningTexts,
            'statusHash' => $statusHash,
            'error' => $error,
            'previewGroupName' => $previewGroupName,
        ]);
    }

    /**
     * Display error page (e.g. preview group not found). Stable fallback hash
     * so the kiosk does not enter a reload loop on a 404.
     */
    private function errorView(string $message, int $status): Response
    {
        return response()->view('display.show', [
            'slides' => [],
            'config' => $this->fallbackConfig(),
            'facilitySlots' => [],
            'eventSlides' => [],
            'runningTexts' => collect(),
            'statusHash' => self::HASH_FALLBACK,
            'error' => $message,
            'previewGroupName' => null,
        ], $status);
    }

    private function safeConfig(): array
    {
        try {
            $config = Setting::displayConfig();
            if (! is_array($config)) {
                return $this->fallbackConfig();
            }

            return array_merge($this->fallbackConfig(), $config);
        } catch (Throwable $e) {
            Log::error('Display config failed', ['message' => $e->getMessage()]);

            return $this->fallbackConfig();
        }
    }

    /**
     * Default config used by the controller and the global exception fallback.
     */
    public static function defaultConfig(): array
    {
        return [
            'logo' => null,
            'logo_text' => '',
            'eyebrow' => 'Event Saat Ini',
            'title' => 'Event Title',
            'section_chip' => 'Fasilitas',
            'show_facility_captions' => true,
            'next_event_label' => 'Event Selanjutnya',
            'next_event_title' => 'Event Berikutnya',
            'next_event_organizer' => '',
            'next_event_date' => '',
            'next_event_time' => '',
            'next_event_location' => '',
            'next_event_category' => '',
            'footer_title' => '',
            'footer_subtitle' => '',
            'footer_support' => '',
            'clock_offset' => 0,
        ];
    }

    private function fallbackConfig(): array
    {
        return self::defaultConfig();
    }

    private function safeFacilities()
    {
        try {
            return Facility::orderBy('slot')->get();
        } catch (Throwable $e) {
            Log::error('Display facilities failed', ['message' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Facility slots keyed by slot number. Slides come from a per-slot provider,
     * which is the assigned facility slot normally and the preview group in
     * preview mode.
     */
    private function buildFacilitySlots($facilities, callable $slidesForSlot): array
    {
        $slots = [];

        foreach ($facilities as $facility) {
            try {
                $slots[$facility->slot] = [
                    'facility' => $facility,
                    'slides' => $slidesForSlot($facility->slot),
                ];
            } catch (Throwable $e) {
                Log::warning('Skipping facility slot', [
                    'slot' => $facility->slot ?? null,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return $slots;
    }

    private function safeSlotSlides(string $slot): array
    {
        try {
            return $this->slotSlides($slot);
        } catch (Throwable $e) {
            Log::error('Display slot slides failed', [
                'slot' => $slot,
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    private function safeRunningTexts()
    {
        try {
            return RunningText::activeOrdered()->get();
        } catch (Throwable $e) {
            Log::error('Display running texts failed', ['message' => $e->getMessage()]);

            return collect();
        }
    }

    private function safeContentHash(): string
    {
        try {
            return $this->contentHash();
        } catch (Throwable $e) {
            Log::error('Display content hash failed', ['message' => $e->getMessage()]);

            return self::HASH_FALLBACK;
        }
    }

    /**
     * Ordered media playlist for a display slot.
     */
    private function slotSlides(string $slot): array
    {
        $slides = [];

        foreach (DisplaySlotGroup::orderedFor($slot) as $placement) {
            try {
                $group = $placement->group;
                if (! $group || ! $group->is_active) {
                    continue;
                }

                foreach ($group->activePhotos()->with('photo')->get() as $item) {
                    $slide = $this->mapItemToSlide($item, $group, $this->framingKeyForSlot($slot));
                    if ($slide !== null) {
                        $slides[] = $slide;
                    }
                }
            } catch (Throwable $e) {
                Log::warning('Skipping broken display placement', [
                    'slot' => $slot,
                    'placement_id' => $placement->id ?? null,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return $slides;
    }

    /**
     * Map a physical display slot to its framing key: all facility frames
     * share one framing set since they render at identical aspect ratios.
     */
    private function framingKeyForSlot(string $slot): string
    {
        if (str_starts_with($slot, 'facility_')) {
            return 'facilities';
        }

        return in_array($slot, ['main', 'next_event'], true) ? $slot : 'main';
    }

    /**
     * Map a group item to a slide payload. Returns null when unusable.
     */
    private function mapItemToSlide($item, PhotoGroup $group, string $framingKey = 'main'): ?array
    {
        $photo = $item->photo ?? null;
        if (! $photo) {
            return null;
        }

        $path = trim((string) ($photo->file_path ?? ''));
        if ($path === '') {
            return null;
        }

        $type = $photo->type ?? 'photo';
        if (! in_array($type, ['photo', 'video'], true)) {
            $type = 'photo';
        }

        $duration = (int) ($group->slide_duration ?? 5);
        if ($duration < 1) {
            $duration = 5;
        }

        $fill = $type === 'video' ? 'contain' : ($group->fill_mode ?? 'cover');
        if (! in_array($fill, ['cover', 'contain'], true)) {
            $fill = 'cover';
        }

        $framing = $photo->framingFor($framingKey);
        $focusX = $framing['fx'];
        $focusY = $framing['fy'];
        $zoom = $framing['zoom'];

        return [
            'url' => asset('storage/'.ltrim($path, '/')),
            'type' => $type,
            'duration' => $duration,
            'transition' => $group->transition_type ?? 'fade',
            'group' => $group->name ?? '',
            'fill' => $fill,
            'focusX' => $focusX,
            'focusY' => $focusY,
            'zoom' => $zoom,
        ];
    }

    /**
     * Ordered playlist of every active media in a group (preview mode).
     */
    private function safeGroupSlides(PhotoGroup $group, string $framingKey = 'main'): array
    {
        try {
            $slides = [];

            foreach ($group->activePhotos()->with('photo')->get() as $item) {
                $slide = $this->mapItemToSlide($item, $group, $framingKey);
                if ($slide !== null) {
                    $slides[] = $slide;
                }
            }

            return $slides;
        } catch (Throwable $e) {
            Log::error('Display group slides failed', [
                'group_id' => $group->id,
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Cached content fingerprint for status polling.
     */
    private function contentHash(): string
    {
        return Cache::remember(
            'display.content_hash',
            self::STATUS_HASH_CACHE_SECONDS,
            fn () => $this->computeContentHash()
        );
    }

    /**
     * Hash of every value rendered on the display page.
     */
    private function computeContentHash(): string
    {
        $payload = [
            'settings' => Setting::displayConfig(),
            'groups' => PhotoGroup::with('items')->get()->map(fn ($g) => [
                'id' => $g->id,
                'is_active' => $g->is_active,
                'slide_duration' => $g->slide_duration,
                'transition_type' => $g->transition_type,
                'fill_mode' => $g->fill_mode,
                'updated_at' => optional($g->updated_at)->toJSON(),
                'items' => $g->items->map(fn ($i) => [
                    'photo_id' => $i->photo_id,
                    'is_active' => $i->is_active,
                    'sort_order' => $i->sort_order,
                    'updated_at' => optional($i->updated_at)->toJSON(),
                ]),
            ]),
            'photos' => Photo::all()->map(fn ($p) => [
                'id' => $p->id,
                'file_path' => $p->file_path,
                'type' => $p->type,
                'focus_x' => $p->focus_x,
                'focus_y' => $p->focus_y,
                'crop_zoom' => $p->crop_zoom,
                'crop_data' => $p->crop_data,
                'updated_at' => optional($p->updated_at)->toJSON(),
            ]),
            'facilities' => Facility::orderBy('slot')->get()->map(fn ($f) => [
                'slot' => $f->slot,
                'caption' => $f->caption,
                'updated_at' => optional($f->updated_at)->toJSON(),
            ]),
            'placements' => DisplaySlotGroup::all()->map(fn ($p) => [
                'slot' => $p->slot,
                'photo_group_id' => $p->photo_group_id,
                'sort_order' => $p->sort_order,
                'updated_at' => optional($p->updated_at)->toJSON(),
            ]),
            'running_texts' => RunningText::all()->map(fn ($t) => [
                'text' => $t->text,
                'is_active' => $t->is_active,
                'sort_order' => $t->sort_order,
                'updated_at' => optional($t->updated_at)->toJSON(),
            ]),
        ];

        return md5((string) json_encode($payload));
    }

    /**
     * Drop the cached status hash (e.g. after content mutations in tests).
     */
    public static function forgetStatusHashCache(): void
    {
        Cache::forget('display.content_hash');
    }
}
