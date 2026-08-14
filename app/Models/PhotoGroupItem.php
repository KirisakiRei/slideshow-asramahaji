<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhotoGroupItem extends Model
{
    protected $fillable = ['photo_group_id', 'photo_id', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(PhotoGroup::class, 'photo_group_id');
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }
}
