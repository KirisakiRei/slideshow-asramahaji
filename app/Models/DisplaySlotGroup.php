<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisplaySlotGroup extends Model
{
    public const SLOT_MAIN = 'main';
    public const SLOT_FACILITY_1 = 'facility_1';
    public const SLOT_FACILITY_2 = 'facility_2';
    public const SLOT_FACILITY_3 = 'facility_3';
    public const SLOT_NEXT_EVENT = 'next_event';

    public const SLOTS = [
        self::SLOT_MAIN,
        self::SLOT_FACILITY_1,
        self::SLOT_FACILITY_2,
        self::SLOT_FACILITY_3,
        self::SLOT_NEXT_EVENT,
    ];

    protected $fillable = ['slot', 'photo_group_id', 'sort_order'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(PhotoGroup::class, 'photo_group_id');
    }

    /**
     * Ordered list of placements for a given display slot.
     */
    public static function orderedFor(string $slot): Collection
    {
        return static::where('slot', $slot)
            ->orderBy('sort_order')
            ->with('group')
            ->get();
    }

    /**
     * Replace the whole ordered group list of a slot.
     */
    public static function sync(string $slot, array $groupIds): void
    {
        static::where('slot', $slot)->delete();

        foreach (array_values(array_unique($groupIds)) as $index => $groupId) {
            static::create([
                'slot' => $slot,
                'photo_group_id' => $groupId,
                'sort_order' => $index,
            ]);
        }
    }
}
