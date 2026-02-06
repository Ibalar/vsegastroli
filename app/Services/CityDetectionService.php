<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\City;

class CityDetectionService
{
    /**
     * Определение города по IP
     */
    public function detectCityByIp(string $ip): ?array
    {
        try {
            // Вариант 1: ipapi.co (бесплатно до 1000 запросов/день)
            $response = Http::get("https://ipapi.co/{$ip}/json/");

            if ($response->successful()) {
                $data = $response->json();
                $cityName = $data['city'] ?? null;

                if ($cityName) {
                    // Ищем город в нашей базе
                    $city = City::active()
                        ->where('name', 'like', "%{$cityName}%")
                        ->first();

                    if ($city) {
                        return [
                            'id' => $city->id,
                            'name' => $city->name,
                            'name_in' => $city->name_in,
                            'slug' => $city->slug,
                            'detected' => true
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('City detection error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Получить список всех активных городов для выбора
     */
    public function getActiveCities(): \Illuminate\Database\Eloquent\Collection
    {
        return City::active()->orderBy('name')->get();
    }
}
