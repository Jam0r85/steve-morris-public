<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertySlugRedirect extends Model
{
    protected $fillable = ['property_id', 'old_slug'];
}
