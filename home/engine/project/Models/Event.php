<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\FilterableEvents;

class Event extends Model
{
    use HasFactory, FilterableEvents;
    protected $fillable = [
        'title',
        'description',
        'slug',
        'category_id',
        'city_id',
        'venue_id',
        'start_date',
        'poster_url',
        'images',
        'organizer_code',
        'booking_code',
        'price_min',
        'price_max',
        'is_popular',
        'is_new',
        'status',
        'meta_title',
        'meta_description',
    ];
    protected $casts = [
        'category_id' => 'integer',
        'city_id' => 'integer',
        'venue_id' => 'integer',
        'start_date' => 'datetime',
        'images' => 'array',
        'price_min' => 'decimal:2',
        'price_max' => 'decimal:2',
        'is_popular' => 'boolean',
        'is_new' => 'boolean',
    ];

    /**
     * Связь: мероприятие принадлежит категории
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Связь: мероприятие принадлежит городу
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Связь: мероприятие принадлежит площадке
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Scope: только опубликованные мероприятия
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope: популярные мероприятия
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    /**
     * Scope: новые мероприятия
     */
    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    /**
     * Scope: фильтр по диапазону дат
     */
    public function scopeDateRange($query, $dateFrom = null, $dateTo = null)
    {
        if ($dateFrom) {
            $query->where('start_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('start_date', '<=', $dateTo);
        }
        return $query;
    }

    /**
     * Scope: текстовый поиск
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Scope: сортировка по дате начала
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc');
    }

    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Черновик',
            'published' => 'Опубликовано',
            'cancelled' => 'Отменено',
            default => $this->status
        };
    }
}