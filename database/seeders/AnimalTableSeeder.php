<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class AnimalTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fake = Faker::create();

        $habitatIds = DB::table('habitats')->pluck('id')->toArray();

        if(empty($habitatIds))
            {
                $this->command->warn('No hay habitats en la tabla habitats');
                return;
            }
        
        $speciesIds = DB::table('species')->pluck('id')->toArray();

        if(empty($speciesIds))
            {
                $this->command->warn('No hay especies en la tabla species');
                return;
            }
        
        $animals = [];

        for ($i = 0; $i < 50; $i++)
            {
                $animals[] = [
                    'habitat_id' => $fake->randomElement($habitatIds),
                    'species_id' => $fake->randomElement($speciesIds),
                    'code' => 'ANI-' . $fake->unique()->numberBetween(1000, 9999),
                    'name' => $fake->firstName(),
                    'sex' => $fake->randomElement(['male', 'female', 'unknown']),
                    'birth_date' => $fake->dateTimeBetween('-10 years', '-1 year'),
                    'admission_date' => $fake->dateTimeBetween('-1 year', 'now'),
                    'health_status' => $fake->randomElement(['healthy', 'treatment', 'quarantine']),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        
        DB::table('animals')->insert($animals);
    }
}
