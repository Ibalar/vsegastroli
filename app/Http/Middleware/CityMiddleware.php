<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\City;
use Illuminate\Support\Facades\Cookie;

class CityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем наличие города в cookie
        $cityData = $request->cookie('selected_city');

        if ($cityData) {
            $cityData = json_decode($cityData, true);
            $city = City::active()->find($cityData['id']);

            // Если город существует и активен
            if ($city) {
                $request->merge(['current_city' => $city]);
                view()->share('currentCity', $city);
                return $next($request);
            }
        }

        // Если города нет или он неактивен - показываем модальное окно
        $request->merge(['show_city_modal' => true]);
        view()->share('showCityModal', true);
        view()->share('currentCity', null);

        return $next($request);
    }
}
