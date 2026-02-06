<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city_id',
        'description',
    ];

    protected $casts = [
        'city_id' => 'integer',
    ];

    /**
     * Связь: площадка принадлежит городу
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }


    /**
     * Связь: у площадки много мероприятий
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
