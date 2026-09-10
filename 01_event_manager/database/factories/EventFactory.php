<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),  // generál egy véletlenszerű címet
            'description' => fake()->paragraph(),  // generál egy véletlenszerű leírást
            'event_date' => fake()->dateTimeBetween('now', '+3 months'),  // generál egy véletlenszerű dátumot a jövőben
        ];
    }
}
