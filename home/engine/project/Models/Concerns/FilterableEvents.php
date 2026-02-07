<?php

namespace App\Models\Concerns;

use App\Http\Requests\EventFilterRequest;
use App\Models\City;
use Illuminate\Database\Eloquent\Builder;

/**
 * Трейт для централизованной фильтрации событий
 * 
 * Предоставляет единый метод applyFilters() для применения всех типов фильтров:
 * - текстовый поиск
 * - фильтрация по датам
 * - фильтрация по городу
 * 
 * Использование:
 * class Event extends Model
 * {
 *     use FilterableEvents;
 * }
 * 
 * $events = Event::query()->applyFilters($request);
 */
trait FilterableEvents
{
    /**
     * Scope: применить все фильтры из EventFilterRequest
     * 
     * @param Builder $query
     * @param EventFilterRequest $request
     * @return Builder
     */
    public function scopeApplyFilters(Builder $query, EventFilterRequest $request): Builder
    {
        // Применяем фильтры в нужном порядке
        return $this->applyCityFilter(
            $this->applyDateFilter(
                $this->applySearchFilter($query, $request),
                $request
            ),
            $request
        );
    }

    /**
     * Применить текстовый поиск
     * 
     * @param Builder $query
     * @param EventFilterRequest $request
     * @return Builder
     */
    private function applySearchFilter(Builder $query, EventFilterRequest $request): Builder
    {
        $searchQuery = $request->getSearchQuery();
        
        if (!$searchQuery) {
            return $query;
        }

        // Используем существующий scope search() если он есть,
        // иначе применяем базовую логику поиска
        if (method_exists($this, 'scopeSearch')) {
            return $query->search($searchQuery);
        }

        // Fallback для моделей без scope search
        return $query->where(function ($q) use ($searchQuery) {
            $q->where('title', 'like', "%{$searchQuery}%")
              ->orWhere('description', 'like', "%{$searchQuery}%");
        });
    }

    /**
     * Применить фильтрацию по датам
     * 
     * @param Builder $query
     * @param EventFilterRequest $request
     * @return Builder
     */
    private function applyDateFilter(Builder $query, EventFilterRequest $request): Builder
    {
        $startDate = $request->getStartDate();
        $endDate = $request->getEndDate();

        if ($startDate) {
            // Используем whereDate для точного сравнения дат (без времени)
            $query->whereDate('start_date', '>=', $startDate);
        }

        if ($endDate) {
            // Используем whereDate для точного сравнения дат (без времени)
            $query->whereDate('start_date', '<=', $endDate);
        }

        return $query;
    }

    /**
     * Применить фильтрацию по городу
     * 
     * @param Builder $query
     * @param EventFilterRequest $request
     * @return Builder
     */
    private function applyCityFilter(Builder $query, EventFilterRequest $request): Builder
    {
        $citySlug = $request->input('city_slug');
        
        if (!$citySlug) {
            return $query;
        }

        $cityId = City::where('slug', $citySlug)->value('id');
        
        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        return $query;
    }
}