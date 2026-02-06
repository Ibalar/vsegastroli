<?php

namespace App\Helpers;

use App\Models\City;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Support\Facades\Route;

class Breadcrumbs
{
    public static function generate(): array
    {
        $routeName = Route::currentRouteName();
        $params = Route::current()->parameters();
        $crumbs = [];

        // Главная
        $crumbs[] = [
            'title' => 'Главная',
            'url' => route('main')
        ];

        // Городская главная
        if (isset($params['city'])) {
            $city = City::where('slug', $params['city'])->first();
            if ($city) {
                $crumbs[] = [
                    'title' => $city->name,
                    'url' => route('home', ['city' => $city->slug])
                ];
            }
        }

        // Категория в городе
        if (isset($params['category'])) {
            $category = Category::where('slug', $params['category'])->first();
            if ($category && isset($params['city'])) {
                $crumbs[] = [
                    'title' => $category->name,
                    'url' => route('category.show', [
                        'city' => $params['city'],
                        'category' => $params['category']
                    ])
                ];
            }
        }

        // Мероприятие
        if ($routeName === 'event.show' && isset($params['slug'])) {
            $event = Event::where('slug', $params['slug'])->first();
            if ($event) {
                $crumbs[] = [
                    'title' => $event->title,
                ];
            }
        }

        return $crumbs;
    }
}

