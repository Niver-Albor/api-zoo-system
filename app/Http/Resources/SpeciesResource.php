<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpeciesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'common_name'       => $this->common_name,
            'scientific_name'   => $this->scientific_name,
            'description'       => $this->description,
            'animal_count'      => $this->whenLoaded('animals_count', fn () => $this->animals_count),
            'created_at'        => $this->created_at?->toIso8601String(),
        ];
    }
}
