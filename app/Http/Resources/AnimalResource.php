<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnimalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'          => $this->name,
            'code'          => $this->code,
            'sex'           => $this->sex,
            'health_status'     => $this->health_status,
            'birth_date'        => $this->birth_date ?
                Carbon::parse($this->birth_date)->toIso8601String() : null,
            'admission_date'    => $this->admission_date ?
                Carbon::parse($this->admission_date)->toIso8601String() : null,
            'species'           => $this->whenLoaded('species', function () {
                return [
                    'id'            => $this->species->id,
                    'common_name'   => $this->species->common_name,
                ];
            }),
            'habitat'           => $this->whenLoaded('habitat', function () {
                return [
                    'id'    => $this->habitat->id,
                    'name'  => $this->habitat->name,
                ];
            })
        ];
    }
}
