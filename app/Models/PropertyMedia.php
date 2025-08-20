<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyMedia extends Model
{
    protected $fillable = [
        'property_id', 'category', 'url', 'sort_order', 'width', 'height',
        'is_image', 'media_type', 'title',
        'url_thumbnail', 'url_small', 'url_medium', 'url_large', 'url_hero', 'url_full',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
