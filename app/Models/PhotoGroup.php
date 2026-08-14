<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhotoGroup extends Model
{
    protected $fillable = ['name', 'description', 'is_active', 'slide_duration', 'sort_order', 'transition_type', 'fill_mode'];

    protected $casts = [
        'is_active' => 'boolean',
        'slide_duration' => 'integer',
        'sort_order' => 'integer',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PhotoGroupItem::class);
    }

    public function activePhotos(): HasMany
    {
        return $this->items()
            ->where('photo_group_items.is_active', true)
            ->whereHas('photo', fn ($query) => $query->where('is_active', true))
            ->orderBy('sort_order');
    }
}
