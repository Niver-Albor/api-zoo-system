<?php

namespace App\Business\Services;

use App\Models\Animal;


class IndexAnimalService
{
    public function indexAnimals(array $data)
    {
        $allAnimals = Animal::
            when($data['health_status'], function ($query, $health_status) {
                $query->where('health_status', $health_status);
            })
            ->when($data['species_id'], function ($query, $species_id) {
                $query->where('species_id', $species_id);
            })
            ->when($data['habitat_id'], function ($query, $habitat_id) {
                $query->where('habitat_id', $habitat_id);
            })
            ->when($data['sex'], function ($query, $sex) {
                $query->where('sex', $sex);
            })
            ->when($data['search'], function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%");
                });
            })
            ->skip($data['offset'])->take($data['per_page'])->get();

        return $allAnimals->toArray();
    }
}