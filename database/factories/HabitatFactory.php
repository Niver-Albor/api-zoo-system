<?php

namespace Database\Factories;

use App\Models\Habitat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Habitat>
 */
class HabitatFactory extends Factory
{
    protected $model = Habitat::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => strtoupper(trim($this->faker->unique()->sentence(2))),
            'type' => $this->faker->randomElement(['aviary', 'aquatic', 'terrestrial']),
            'capacity' => $this->faker->randomDigit(),
            'status' => $this->faker->randomElement(['available', 'maintenance'])
        ];
    }
}
