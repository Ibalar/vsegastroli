<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class City extends Model
{
    use HasFactory;

    private const string CACHE_KEY = 'cities:active';
    private const int CACHE_TTL = 3600; // 1 hour

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
     * Boot method for model events
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }

    /**
     * Get active cities from cache or database
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     */
    public static function getActiveCities()
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return static::where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Clear the active cities cache
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

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
