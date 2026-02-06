<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MainController;


// Главная без города
Route::get('/', [MainController::class, 'index'])->name('main');

// Категория без города (например: /teatr)
Route::get('/category/{category}', [CategoryController::class, 'showNoCity'])->name('category.show_no_city');

// Главная страница с городом
Route::get('/{city}', [EventController::class, 'index'])->name('home');

// Страница "Все мероприятия" для города /belgorod/all-events
Route::get('/{city}/all-events', [CategoryController::class, 'allEvents'])->name('city.all-events');

// Категория с городом (например: /belgorod/teatr)
Route::get('/{city}/{category}', [CategoryController::class, 'show'])->name('category.show');

// Мероприятие всегда с городом (например: /belgorod/super-concert)
Route::get('/{city}/event/{slug}', [EventController::class, 'show'])->name('event.show');




// API для работы с городами
Route::prefix('api')->group(function () {
    Route::get('/city/detect', [CityController::class, 'detect'])->name('api.city.detect');
    Route::post('/city/set', [CityController::class, 'setCity'])->name('api.city.set');
    Route::get('/city/list', [CityController::class, 'list'])->name('api.city.list');
});


// Подключаем маршруты Moonshine
require __DIR__.'/moonshine.php';
