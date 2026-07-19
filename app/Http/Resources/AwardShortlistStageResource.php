<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AwardShortlistStageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'slug' => $this->slug,
            'award_type' => $this->award_type,
            'stage_engine' => $this->stage_engine,
            'system_conditions' => $this->system_conditions,
            'position' => $this->position,
            'active' => $this->active,
            'mark_as_final' => $this->mark_as_final,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
