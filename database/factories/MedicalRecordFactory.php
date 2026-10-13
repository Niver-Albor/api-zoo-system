<?php

namespace Database\Factories;

use App\Models\Animal;
use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalRecord>
 */
class MedicalRecordFactory extends Factory
{
    protected $model = MedicalRecord::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'animal_id' => Animal::inRandomOrder()->first()->id ?? Animal::factory(),
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'replaces_id' => $this->faker->optional(0.2)->randomElement(MedicalRecord::inRandomOrder()->first()->id ?? MedicalRecord::factory()),
            'uuid' => $this->faker->unique()->uuid(),
            'record_type' => $this->faker->randomElement(['checkup', 'vaccination', 'deworming', 'emergency', 'correction']),
            'diagnosis' => $this->faker->text(255),
            'treatment' => $this->faker->text(255),
            'performed_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'next_checkup_at' => $this->faker->optional(0.7)->dateTimeBetween('now', '+6 months'),
        ];
    }
}
