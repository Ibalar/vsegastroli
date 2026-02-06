<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_in',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Связь: у города много мероприятий
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Связь: у города много площадок
     */
    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    /**
     * Scope: только активные города
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
