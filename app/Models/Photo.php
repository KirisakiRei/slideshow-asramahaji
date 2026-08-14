<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Photo extends Model
{
    public const FRAMING_SLOTS = ['main', 'facilities', 'next_event'];

    protected $fillable = ['title', 'file_path', 'type', 'is_active', 'focus_x', 'focus_y', 'crop_zoom', 'crop_data'];

    protected $casts = [
        'is_active' => 'boolean',
        'focus_x' => 'integer',
        'focus_y' => 'integer',
        'crop_zoom' => 'integer',
        'crop_data' => 'array',
    ];

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isPhoto(): bool
    {
        return $this->type === 'photo';
    }

    /**
     * Normalized framing for a display slot key ('main', 'facilities', 'next_event').
     * Falls back to the legacy single framing when no per-slot data exists.
     *
     * @return array{fx: int, fy: int, zoom: int}
     */
    public function framingFor(string $slot): array
    {
        $legacyFx = (int) ($this->focus_x ?? 50);
        $legacyFy = (int) ($this->focus_y ?? 50);
        $legacyZoom = (int) ($this->crop_zoom ?? 100);

        $data = $this->crop_data;
        if (is_array($data) && isset($data[$slot]) && is_array($data[$slot])) {
            return [
                'fx' => max(0, min(100, (int) ($data[$slot]['fx'] ?? $legacyFx))),
                'fy' => max(0, min(100, (int) ($data[$slot]['fy'] ?? $legacyFy))),
                'zoom' => max(100, min(400, (int) ($data[$slot]['zoom'] ?? $legacyZoom))),
            ];
        }

        return [
            'fx' => max(0, min(100, $legacyFx)),
            'fy' => max(0, min(100, $legacyFy)),
            'zoom' => max(100, min(400, $legacyZoom)),
        ];
    }

    public function groupItems(): HasMany
    {
        return $this->hasMany(PhotoGroupItem::class);
    }
}
