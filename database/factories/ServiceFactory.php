<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'duration' => $this->faker->randomElement([30, 60, 120]),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 100, 5000),
            'is_active' => true,
        ];
    }

    /**
     * Услугу "Поездка на квадроцикле"
     */
    public function quadBike(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Поездка на квадроцикле',
            'description' => 'Увлекательная поездка на квадроцикле по пересеченной местности',
            'price' => 2500.00,
        ]);
    }

    /**
     * Услугу "Тур на эндуро"
     */
    public function enduroTour(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Тур на эндуро',
            'description' => 'Экстремальный тур на эндуро мотоцикле',
            'price' => 3500.00,
        ]);
    }
}
