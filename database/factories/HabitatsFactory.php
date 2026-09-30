<?php

namespace Database\Factories;

use App\Models\Habitats;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Habitats>
 */
class HabitatsFactory extends Factory
{
    protected $model = Habitats::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->sentence(2),
            'type' => $this->faker->randomElement(['aviary', 'aquatic', 'terrestrial']),
            'capacity' => $this->faker->randomDigit(),
            'status' => $this->faker->randomElement(['available', 'maintenance'])
        ];
    }
}
