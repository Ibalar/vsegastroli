<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventFilterRequest;
use App\Models\Category;
use App\Models\City;
use App\Models\Event;

class CategoryController extends Controller
{
    /**
     * Страница категории с фильтрами
     */
    public function show(EventFilterRequest $request, $city, $category)
    {
        $currentCity = City::active()->where('slug', $city)->firstOrFail();
        $currentCategory = Category::active()->where('slug', $category)->firstOrFail();
        $cityIn = $currentCity->name_in ?? null;

        // Запрос мероприятий с централизованной фильтрацией
        $eventsQuery = Event::with(['venue'])
            ->where('city_id', $currentCity->id)
            ->where('category_id', $currentCategory->id)
            ->published()
            ->applyFilters($request);

        // Определяем заголовок страницы
        if ($currentCategory && $cityIn) {
            $pageTitle = "{$currentCategory->name} в {$cityIn}";
        } elseif ($currentCategory) {
            $pageTitle = $currentCategory->name;
        } elseif ($cityIn) {
            $pageTitle = "Мероприятия {$cityIn}";
        } else {
            $pageTitle = 'Все мероприятия';
        }

        // Сортировка
        $events = $eventsQuery->upcoming()->paginate(24);
        $categories = Category::getAllActive();

        return view('category.show', compact('currentCity', 'currentCategory', 'events', 'categories', 'pageTitle'));
    }

    public function showNoCity(EventFilterRequest $request, $category)
    {
        // Находим категорию
        $currentCategory = Category::active()->where('slug', $category)->firstOrFail();

        // Мероприятия по категории из всех городов с централизованной фильтрацией
        $eventsQuery = Event::with(['venue', 'city'])
            ->where('category_id', $currentCategory->id)
            ->published()
            ->applyFilters($request);

        $events = $eventsQuery->upcoming()->paginate(24);

        return view('category.show_no_city', compact('currentCategory', 'events'));
    }

    public function allEvents(EventFilterRequest $request, $city)
    {
        $currentCity = \App\Models\City::active()->where('slug', $city)->firstOrFail();
        $currentCategory = null; // это страница "все", не категория
        $cityIn = $currentCity->name_in ?? null;

        // Запрос событий этого города, любых категорий с централизованной фильтрацией
        $eventsQuery = \App\Models\Event::with(['category', 'venue'])
            ->where('city_id', $currentCity->id)
            ->published()
            ->applyFilters($request);

        $pageTitle = $cityIn
            ? "Все мероприятия в {$cityIn}"
            : 'Все мероприятия';

        $categories = Category::getAllActive();
        $events = $eventsQuery->upcoming()->paginate(24);

        return view('city.all-events', compact(
            'currentCity',
            'currentCategory',
            'events',
            'categories',
            'pageTitle'
        ));
    }
}