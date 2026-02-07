<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Event;
use App\Models\City;
use Illuminate\Support\Facades\Cache;

class CategoryCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_home_categories_caching(): void
    {
        // Создаем тестовые данные
        City::factory()->create(['name' => 'Москва', 'slug' => 'moscow', 'is_active' => true]);
        
        Category::factory()->create([
            'name' => 'Концерты',
            'is_active' => true,
            'show_on_home' => true,
            'sort_order' => 1,
        ]);

        Category::factory()->create([
            'name' => 'Спорт',
            'is_active' => true,
            'show_on_home' => true,
            'sort_order' => 2,
        ]);

        // Первое обращение - должно выполнить запрос к БД
        $startTime = microtime(true);
        $categories1 = Category::getHomeCategories();
        $firstCallTime = microtime(true) - $startTime;

        // Очищаем счетчик времени
        usleep(1000); // Небольшая задержка

        // Второе обращение - должно взять данные из кэша
        $startTime = microtime(true);
        $categories2 = Category::getHomeCategories();
        $secondCallTime = microtime(true) - $startTime;

        // Проверяем, что результаты одинаковые
        $this->assertEquals($categories1->count(), $categories2->count());
        $this->assertEquals($categories1->first()->name, $categories2->first()->name);

        // Второй вызов должен быть быстрее или примерно таким же (из кэша)
        // Допускаем погрешность в 2 раза, так как разница может быть минимальной
        $this->assertLessThanOrEqual($firstCallTime * 2, $secondCallTime * 2);
    }

    public function test_get_all_active_caching(): void
    {
        Category::factory()->create([
            'name' => 'Театр',
            'is_active' => true,
            'show_on_home' => false,
            'sort_order' => 1,
        ]);

        Category::factory()->create([
            'name' => 'Кино',
            'is_active' => false,
            'show_on_home' => false,
            'sort_order' => 2,
        ]);

        $categories = Category::getAllActive();

        // Проверяем, что вернулись только активные категории
        $this->assertEquals(1, $categories->count());
        $this->assertEquals('Театр', $categories->first()->name);
        $this->assertTrue($categories->first()->is_active);
    }

    public function test_cache_invalidation_on_save(): void
    {
        // Очищаем кэш
        Cache::forget('categories:home');
        Cache::forget('categories:active');

        // Заполняем кэш
        $initialCategories = Category::getHomeCategories();

        // Создаем новую категорию
        Category::factory()->create([
            'name' => 'Новая категория',
            'is_active' => true,
            'show_on_home' => true,
            'sort_order' => 1,
        ]);

        // Получаем категории после сохранения
        $updatedCategories = Category::getHomeCategories();

        // Проверяем, что количество изменилось (кэш был инвалидирован)
        $this->assertEquals($initialCategories->count() + 1, $updatedCategories->count());
    }

    public function test_cache_invalidation_on_delete(): void
    {
        // Создаем категорию и заполняем кэш
        $category = Category::factory()->create([
            'name' => 'Категория для удаления',
            'is_active' => true,
            'show_on_home' => true,
            'sort_order' => 1,
        ]);

        $initialCategories = Category::getHomeCategories();

        // Удаляем категорию
        $category->delete();

        // Получаем категории после удаления
        $updatedCategories = Category::getHomeCategories();

        // Проверяем, что количество уменьшилось (кэш был инвалидирован)
        $this->assertEquals($initialCategories->count() - 1, $updatedCategories->count());
    }

    public function test_clear_cache_method(): void
    {
        // Заполняем кэш
        Category::getHomeCategories();
        Category::getAllActive();

        // Проверяем, что кэш существует
        $this->assertTrue(Cache::has('categories:home'));
        $this->assertTrue(Cache::has('categories:active'));

        // Очищаем кэш
        Category::clearCache();

        // Проверяем, что кэш очищен
        $this->assertFalse(Cache::has('categories:home'));
        $this->assertFalse(Cache::has('categories:active'));
    }

    public function test_sorting_by_sort_order(): void
    {
        Category::factory()->create([
            'name' => 'Последняя',
            'is_active' => true,
            'show_on_home' => true,
            'sort_order' => 3,
        ]);

        Category::factory()->create([
            'name' => 'Первая',
            'is_active' => true,
            'show_on_home' => true,
            'sort_order' => 1,
        ]);

        Category::factory()->create([
            'name' => 'Вторая',
            'is_active' => true,
            'show_on_home' => true,
            'sort_order' => 2,
        ]);

        $categories = Category::getHomeCategories();

        // Проверяем порядок сортировки
        $this->assertEquals('Первая', $categories->get(0)->name);
        $this->assertEquals('Вторая', $categories->get(1)->name);
        $this->assertEquals('Последняя', $categories->get(2)->name);
    }
}