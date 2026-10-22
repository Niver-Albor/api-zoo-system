<?php

namespace Database\Factories;

use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Species>
 */
class SpeciesFactory extends Factory
{
    protected $model = Species::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'common_name' => strtoupper(trim($this->faker->unique()->words(2, true))),
            'scientific_name' => strtoupper(trim($this->faker->unique()->words(3, true))),
            'description' => $this->faker->sentence()
        ];
    }
}
