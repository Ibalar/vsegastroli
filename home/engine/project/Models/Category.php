<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
        'is_active',
        'show_on_home',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_on_home' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Связь: у категории много мероприятий
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Scope: только активные категории
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: категории для главной страницы
     */
    public function scopeOnHome($query)
    {
        return $query->where('show_on_home', true);
    }

    /**
     * Scope: сортировка по порядку
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Получение категорий для главной страницы с кэшированием
     */
    public static function getHomeCategories()
    {
        return Cache::remember('categories:home', 3600, function () {
            return static::active()
                ->onHome()
                ->ordered()
                ->get();
        });
    }

    /**
     * Получение всех активных категорий с кэшированием
     */
    public static function getAllActive()
    {
        return Cache::remember('categories:active', 3600, function () {
            return static::active()
                ->ordered()
                ->get();
        });
    }

    /**
     * Очистка кэша категорий
     */
    public static function clearCache()
    {
        Cache::forget('categories:home');
        Cache::forget('categories:active');
    }

    /**
     * Boot метод для автоматической инвалидации кэша
     */
    protected static function boot()
    {
        parent::boot();

        // При сохранении (создание или обновление)
        static::saved(function ($category) {
            static::clearCache();
        });

        // При удалении
        static::deleted(function ($category) {
            static::clearCache();
        });
    }
}