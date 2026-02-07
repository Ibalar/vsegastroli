<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->word;
        
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'sort_order' => $this->faker->numberBetween(1, 100),
            'is_active' => $this->faker->boolean(80), // 80% вероятность быть активной
            'show_on_home' => $this->faker->boolean(60), // 60% вероятность быть на главной
        ];
    }

    /**
     * Indicate that the category is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the category should be shown on home page.
     */
    public function onHome(): static
    {
        return $this->state(fn (array $attributes) => [
            'show_on_home' => true,
        ]);
    }

    /**
     * Indicate that the category should not be shown on home page.
     */
    public function notOnHome(): static
    {
        return $this->state(fn (array $attributes) => [
            'show_on_home' => false,
        ]);
    }
}