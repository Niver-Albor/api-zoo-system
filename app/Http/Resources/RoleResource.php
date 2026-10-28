<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
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
            'description'   => $this->description,
            'permissions'   => $this->whenLoaded('permissions', function () {
                return $this->permissions->map(function ($per) {
                    return [
                        'name'          => $per->name,
                        'description'   => $per->description,
                    ];
                });
            }),
        ];
    }
}
