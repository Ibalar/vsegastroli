<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\City;
use App\Models\Category;

class EventController extends Controller
{
    /**
     * Главная страница города
     */
    public function index(Request $request, $city)
    {
        $currentCity = City::active()->where('slug', $city)->firstOrFail();
        $currentCategory = null;
        $maxSlides = 5;
        $cities = City::getActiveCities();

        $slides = Slide::query()
            ->where('is_active', true)
            ->where('city_id', $currentCity->id)
            ->orderBy('sort_order')
            ->limit($maxSlides)
            ->get();

        if ($slides->isEmpty()) {
            $slides = Slide::query()
                ->where('is_active', true)
                ->inRandomOrder()
                ->limit($maxSlides)
                ->get();
        }

        $categories = Category::getHomeCategories()->map(function ($category) use ($currentCity) {
            $category->events_count = $category->events()
                ->where('city_id', $currentCity->id)
                ->published()
                ->upcoming()
                ->count();
            return $category;
        });

        $homeCategories = Category::getHomeCategories()->map(function ($category) use ($currentCity) {
            $category->load(['events' => function($query) use ($currentCity) {
                $query->where('city_id', $currentCity->id)
                    ->published()
                    ->popular()
                    ->limit(12);
            }]);
            return $category;
        });

        $popularEvents = Event::with(['category', 'venue'])
            ->where('city_id', $currentCity->id)
            ->published()
            ->popular()
            ->upcoming()
            ->limit(12)
            ->get();

        $newEvents = Event::with(['category', 'venue'])
            ->where('city_id', $currentCity->id)
            ->published()
            ->new()
            ->upcoming()
            ->limit(12)
            ->get();

        $query = Event::query()
            ->published();

        if ($city || $request->filled('city_slug')) {
            $slug = $city ?? $request->input('city_slug');
            $cityId = \App\Models\City::where('slug', $slug)->value('id');
            if ($cityId) {
                $query->where('city_id', $cityId);
            }
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function($qWhere) use ($q) {
                $qWhere->where('title', 'like', "%$q%")
                    ->orWhere('venue_name', 'like', "%$q%");
            });
        }

        if ($request->filled('date_start')) {
            $query->whereDate('start_date', '>=', \Carbon\Carbon::createFromFormat('d.m.Y', $request->date_start));
        }
        if ($request->filled('date_end')) {
            $query->whereDate('start_date', '<=', \Carbon\Carbon::createFromFormat('d.m.Y', $request->date_end));
        }

        $events = $query->with(['category','city'])->orderBy('start_date')->paginate(24);
        $cityIn = $currentCity->name_in ?? null;

        return view('home', compact('currentCity', 'categories', 'popularEvents', 'newEvents', 'currentCategory', 'slides', 'cities', 'homeCategories', 'events', 'cityIn'));
    }

    /**
     * Страница мероприятия
     */
    public function show(Request $request, $city, $slug)
    {
        $currentCity = City::active()->where('slug', $city)->firstOrFail();
        $event = Event::with(['category', 'venue'])
            ->where('slug', $slug)
            ->where('city_id', $currentCity->id)
            ->published()
            ->firstOrFail();

        $similarEvents = Event::where('category_id', $event->category_id)
            ->where('city_id', $currentCity->id)
            ->where('id', '!=', $event->id)
            ->published()
            ->upcoming()
            ->limit(6)
            ->get();

        $categories = Category::getAllActive();

        return view('event.show', compact('event', 'currentCity', 'similarEvents', 'categories'));
    }
}
