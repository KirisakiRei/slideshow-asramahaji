<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Photo extends Model
{
    protected $fillable = ['title', 'file_path', 'type', 'is_active', 'focus_x', 'focus_y'];

    protected $casts = [
        'is_active' => 'boolean',
        'focus_x' => 'integer',
        'focus_y' => 'integer',
    ];

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isPhoto(): bool
    {
        return $this->type === 'photo';
    }

    public function groupItems(): HasMany
    {
        return $this->hasMany(PhotoGroupItem::class);
    }
}
