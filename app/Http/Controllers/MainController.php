<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Event;

class MainController extends Controller
{
    public function index(Request $request)
    {
        $cityCookie = $request->cookie('selected_city');

        if ($cityCookie) {
            $cityData = json_decode($cityCookie, true);
            // Проверяем, что slug реально существует и город активен
            if (isset($cityData['slug']) && !empty($cityData['slug'])) {
                $city = City::active()->where('slug', $cityData['slug'])->first();
                if ($city) {
                    // Редиректим на главную выбранного города
                    return redirect()->route('home', ['city' => $city->slug]);
                }
            }
        }

        // Список городов для селекта
        $cities = City::getActiveCities();

        // Категории
        $categories = Category::active()
            ->onHome()
            ->ordered()
            ->withCount(['events' => function($query) {
                $query->published();
            }])
            ->get();

        $homeCategories = Category::active()
            ->onHome()
            ->ordered()
            ->withCount(['events' => function($query) {
                $query->published();
            }])
            ->get();

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

        // Поиск событий (весь сайт)
        $query = Event::query()->published();

        // Если выбран город в поиске — фильтруем по нему
        if ($request->filled('city_slug')) {
            $cityId = City::where('slug', $request->input('city_slug'))->value('id');
            if ($cityId) {
                $query->where('city_id', $cityId);
            }
        }

        // Поиск по тексту
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function($qWhere) use ($q) {
                $qWhere->where('title', 'like', "%$q%")
                    ->orWhere('venue_name', 'like', "%$q%");
            });
        }

        // Фильтр по дате (start_date)
        if ($request->filled('date_start')) {
            $query->whereDate('start_date', '>=', \Carbon\Carbon::createFromFormat('d.m.Y', $request->date_start));
        }
        if ($request->filled('date_end')) {
            $query->whereDate('start_date', '<=', \Carbon\Carbon::createFromFormat('d.m.Y', $request->date_end));
        }

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
