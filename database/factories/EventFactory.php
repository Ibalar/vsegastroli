<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\City;

class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        
        return [
            'title' => $title,
            'description' => $this->faker->paragraph(),
            'slug' => Str::slug($title),
            'category_id' => Category::factory(),
            'city_id' => City::factory(),
            'venue_id' => null, // Можно добавить VenueFactory позже
            'start_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'poster_url' => $this->faker->optional()->imageUrl(),
            'images' => null, // JSON array of images
            'organizer_code' => $this->faker->optional()->bothify('ORG-####'),
            'booking_code' => $this->faker->optional()->bothify('BOOK-####'),
            'price_min' => $this->faker->randomFloat(2, 0, 1000),
            'price_max' => $this->faker->randomFloat(2, 1000, 5000),
            'is_popular' => $this->faker->boolean(20), // 20% вероятность
            'is_new' => $this->faker->boolean(30), // 30% вероятность
            'status' => $this->faker->randomElement(['published', 'draft', 'cancelled']),
            'meta_title' => $this->faker->optional()->sentence(),
            'meta_description' => $this->faker->optional()->paragraph(),
        ];
    }

    /**
     * Indicate that the event is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    /**
     * Indicate that the event is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the event is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Indicate that the event is popular.
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_popular' => true,
        ]);
    }

    /**
     * Indicate that the event is new.
     */
    public function new(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_new' => true,
        ]);
    }

    /**
     * Indicate that the event is upcoming.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => $this->faker->dateTimeBetween('now', '+30 days'),
        ]);
    }

    /**
     * Indicate that the event is in the past.
     */
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }
}