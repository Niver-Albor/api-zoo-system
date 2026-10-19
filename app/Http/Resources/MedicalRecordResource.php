<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'record'    => [
                'id'              => $this->uuid,
                'record_type'       => $this->record_type,
                'diagnosis'         => $this->diagnosis,
                'treatment'         => $this->treatment,
                'performed_at'      => Carbon::parse($this->performed_at)->toIso8601String(),
                'next_checkup_at'   => $this->next_checkup_at ? 
                    Carbon::parse($this->next_checkup_at)->toIso8601String() : null,
                'replaces_record'       => $this->whenLoaded('replacesRecord', function () {
                    return $this->replacesRecord?->uuid;
                }),
                'replaced_by' => $this->whenLoaded('replacedBy', function () {
                    return $this->replacedBy?->uuid;
                }),
            ],
            'animal'    => $this->whenLoaded('animal', function() {
                return [
                    'name'  => $this->animal->name,
                    'code'  => $this->animal->code,
                ];
            }),
            'user'      => $this->whenLoaded('user', function() {
                return [
                    'name'  => $this->user->name,
                    'id'  => $this->user->uuid,
                ];
            })
        ];
    }
}
