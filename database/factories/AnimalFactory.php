<?php

namespace Database\Factories;

use App\Models\Animal;
use App\Models\Habitat;
use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Animal>
 */
class AnimalFactory extends Factory
{
    protected $model = Animal::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'habitat_id' => Habitat::inRandomOrder()->first()?->id ?? Habitat::factory(),
            'species_id' => Species::inRandomOrder()->first()?->id ?? Species::factory(), 
            'code' => strtoupper(trim('ANI-' . $this->faker->unique()->numberBetween(1000, 9999))),
            'name' => $this->faker->firstName(),
            'sex' => $this->faker->randomElement(['male', 'female', 'unknown']),
            'birth_date' => $this->faker->dateTimeBetween('-10 years', '-1 year'),
            'admission_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'health_status' => $this->faker->randomElement(['healthy', 'treatment', 'quarantine'])
            ];
    }
}
