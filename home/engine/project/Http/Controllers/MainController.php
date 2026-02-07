<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventFilterRequest;
use App\Models\Category;
use App\Models\City;
use App\Models\Event;

class MainController extends Controller
{
    public function index(EventFilterRequest $request)
    {
        $cityCookie = $request->cookie('selected_city');
        if ($cityCookie) {
            $cityData = json_decode($cityCookie, true);
            // Проверяем, что slug реально существует и город активен
            if (isset($cityData['slug']) && ! empty($cityData['slug'])) {
                $city = City::active()->where('slug', $cityData['slug'])->first();
                if ($city) {
                    // Редиректим на главную выбранного города
                    return redirect()->route('home', ['city' => $city->slug]);
                }
            }
        }

        // Список городов для селекта
        $cities = City::getActiveCities();

        // Категории с кэшированием
        $categories = Category::getHomeCategories();
        $homeCategories = Category::getHomeCategories()->map(function ($category) {
            $category->loadCount(['events' => function ($query) {
                $query->published();
            }]);
            return $category;
        });

        // Новые мероприятия (без привязки к городу)
        $newEvents = Event::with(['category', 'venue'])
            ->published()
            ->new()
            ->orderBy('start_date', 'desc')
            ->limit(12)
            ->get();

        // Популярные мероприятия (без привязки к городу)
        $popularEvents = Event::with(['category', 'venue'])
            ->published()
            ->popular()
            ->orderBy('start_date', 'desc')
            ->limit(12)
            ->get();

        // Слайды (выбирай нужную логику фильтрации по городам)
        $slides = app(SlideController::class)->index($request)->getData()['slides'];

        // Поиск событий (весь сайт) с централизованной фильтрацией
        $query = Event::query()
            ->published()
            ->applyFilters($request);

        $events = $query->orderBy('start_date')->paginate(24);

        return view('main', compact(
            'categories',
            'newEvents',
            'popularEvents',
            'slides',
            'cities',
            'events',
            'homeCategories',
        ));
    }
}