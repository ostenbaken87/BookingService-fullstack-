<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\BookingStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = $this->faker->dateTimeBetween('now', '+1 month');
        
        return [
            'service_id' => \App\Models\Service::factory(),
            'client_name' => $this->faker->name(),
            'client_phone' => $this->faker->phoneNumber(),
            'start_time' => $startTime,
            'end_time' => $this->faker->dateTimeBetween($startTime, '+2 hours'),
            'status' => $this->faker->randomElement(BookingStatus::cases()),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Подтвержденное бронирование
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::CONFIRMED,
        ]);
    }

    /**
     * Создать бронирование на конкретную дату и время
     */
    public function atDateTime(string $date, string $time, int $durationMinutes = 60): static
    {
        $startTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
        $endTime = $startTime->copy()->addMinutes($durationMinutes + 30); // +30 минут буфера

        return $this->state(fn (array $attributes) => [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => BookingStatus::CONFIRMED,
        ]);
    }
}
