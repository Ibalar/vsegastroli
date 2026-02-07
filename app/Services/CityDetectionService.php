<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\City;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class CityDetectionService
{
    /**
     * TTL для кэша определения города по IP (24 часа)
     */
    private const CACHE_TTL = 86400;

    /**
     * Таймаут HTTP запроса в секундах
     */
    private const HTTP_TIMEOUT = 3;

    /**
     * Количество повторных попыток при ошибке
     */
    private const HTTP_RETRIES = 2;

    /**
     * Задержка между повторными попытками в миллисекундах
     */
    private const HTTP_RETRY_DELAY = 100;

    /**
     * Определение города по IP с кэшированием и fallback
     *
     * @param string $ip IP адрес пользователя
     * @return array|null Данные о городе или null если город не найден
     */
    public function detectCityByIp(string $ip): ?array
    {
        $cacheKey = "city_by_ip_{$ip}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($ip) {
            return $this->detectCityFromApi($ip);
        });
    }

    /**
     * Запрос к API для определения города по IP
     *
     * @param string $ip IP адрес пользователя
     * @return array|null Данные о городе или null при ошибке
     */
    private function detectCityFromApi(string $ip): ?array
    {
        try {
            $response = Http::timeout(self::HTTP_TIMEOUT)
                ->retry(self::HTTP_RETRIES, self::HTTP_RETRY_DELAY)
                ->get("https://ipapi.co/{$ip}/json/");

            if ($response->successful()) {
                $data = $response->json();
                $cityName = $data['city'] ?? null;

                if ($cityName) {
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

            Log::warning('City detection API returned unsuccessful response or empty city', [
                'ip' => $ip,
                'status' => $response->status(),
            ]);
        } catch (ConnectionException $e) {
            Log::warning('City detection connection error', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);
        } catch (RequestException $e) {
            Log::warning('City detection request error', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            Log::warning('City detection unexpected error', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);
        }

        return $this->getDefaultCity();
    }

    /**
     * Получить город по умолчанию (первый активный город из БД)
     *
     * @return array|null Данные о городе с флагом detected: false или null
     */
    private function getDefaultCity(): ?array
    {
        $city = City::active()->orderBy('name')->first();

        if (!$city) {
            return null;
        }

        return [
            'id' => $city->id,
            'name' => $city->name,
            'name_in' => $city->name_in,
            'slug' => $city->slug,
            'detected' => false
        ];
    }

    /**
     * Получить список всех активных городов для выбора
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveCities(): \Illuminate\Database\Eloquent\Collection
    {
        return City::active()->orderBy('name')->get();
    }
}
