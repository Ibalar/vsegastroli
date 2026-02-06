<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    protected $fillable = [
        'title',
        'price',
        'city_id',
        'event_slug',
        'image',
        'is_active',
        'sort_order',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_slug', 'slug');
    }
}
