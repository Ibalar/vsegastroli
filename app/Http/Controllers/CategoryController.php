<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\City;
use App\Models\Event;

class CategoryController extends Controller
{
    /**
     * Страница категории с фильтрами
     */
    public function show(Request $request, $city, $category)
    {
        $currentCity = City::active()->where('slug', $city)->firstOrFail();
        $currentCategory = Category::active()->where('slug', $category)->firstOrFail();
        $cityIn = $currentCity->name_in ?? null;

        // Запрос мероприятий с фильтрами
        $eventsQuery = Event::with(['venue'])
            ->where('city_id', $currentCity->id)
            ->where('category_id', $currentCategory->id)
            ->published();

        // Текстовый поиск
        if ($request->has('search') && $request->search) {
            $eventsQuery->search($request->search);
        }

        // Фильтр по датам
        if ($request->has('date_from')) {
            $eventsQuery->where('start_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $eventsQuery->where('start_date', '<=', $request->date_to);
        }

        if ($currentCategory && $cityIn) {
            $pageTitle = "{$currentCategory->name} в {$cityIn}";
        } elseif ($currentCategory) {
            $pageTitle = $currentCategory->name;
        } elseif ($cityIn) {
            $pageTitle = "Мероприятия {$cityIn}";
        } else {
            $pageTitle = "Все мероприятия";
        }

        // Сортировка
        $events = $eventsQuery->upcoming()->paginate(24);

        $categories = Category::active()->ordered()->get();

        return view('category.show', compact('currentCity', 'currentCategory', 'events', 'categories', 'pageTitle'));
    }

    public function showNoCity(Request $request, $category)
    {
        // Находим категорию
        $currentCategory = Category::active()->where('slug', $category)->firstOrFail();

        // Мероприятия по категории из всех городов
        $eventsQuery = Event::with(['venue', 'city'])
            ->where('category_id', $currentCategory->id)
            ->published();

        // Фильтрация и поиск
        if ($request->has('search') && $request->search) {
            $eventsQuery->search($request->search);
        }
        if ($request->has('date_from')) {
            $eventsQuery->where('start_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $eventsQuery->where('start_date', '<=', $request->date_to);
        }

        $events = $eventsQuery->upcoming()->paginate(24);

        return view('category.show_no_city', compact('currentCategory', 'events'));
    }

    public function allEvents(Request $request, $city)
    {
        $currentCity = \App\Models\City::active()->where('slug', $city)->firstOrFail();
        $currentCategory = null; // это страница "все", не категория
        $cityIn = $currentCity->name_in ?? null;

        // Запрос событий этого города, любых категорий
        $eventsQuery = \App\Models\Event::with(['category', 'venue'])
            ->where('city_id', $currentCity->id)
            ->published();

        // Текстовый поиск
        if ($request->has('search') && $request->search) {
            $eventsQuery->search($request->search);
        }

        // Фильтр по датам
        if ($request->has('date_from')) {
            $eventsQuery->where('start_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $eventsQuery->where('start_date', '<=', $request->date_to);
        }

        $pageTitle = $cityIn
            ? "Все мероприятия в {$cityIn}"
            : "Все мероприятия";

        $categories = \App\Models\Category::active()->ordered()->get();

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
