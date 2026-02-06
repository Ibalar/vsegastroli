<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CityDetectionService;
use App\Models\City;
use Illuminate\Support\Facades\Cookie;

class CityController extends Controller
{
    protected $cityDetectionService;

    public function __construct(CityDetectionService $cityDetectionService)
    {
        $this->cityDetectionService = $cityDetectionService;
    }

    /**
     * Определение города по IP
     */
    public function detect(Request $request)
    {
        $ip = $request->ip();
        $detectedCity = $this->cityDetectionService->detectCityByIp($ip);
        $activeCities = $this->cityDetectionService->getActiveCities();

        return response()->json([
            'detected_city' => $detectedCity,
            'cities' => $activeCities
        ]);
    }

    /**
     * Установка выбранного города
     */
    public function setCity(Request $request)
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id'
        ]);

        $city = City::active()->findOrFail($request->city_id);

        $cityData = [
            'id' => $city->id,
            'name' => $city->name,
            'name_in' => $city->name_in,
            'slug' => $city->slug
        ];

        $cookie = Cookie::make(
            'selected_city',
            json_encode($cityData),
            60 * 24 * 90 // 90 дней
        );

        return response()->json([
            'success' => true,
            'city' => $cityData,
            'redirect_url' => route('home', ['city' => $city->slug])
        ])->cookie($cookie);
    }

    /**
     * Получить список активных городов
     */
    public function list()
    {
        $cities = $this->cityDetectionService->getActiveCities();
        return response()->json($cities);
    }
}
