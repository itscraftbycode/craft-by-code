<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the service resource into an array with public fields only.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'badge' => $this->badge,
            'icon' => $this->icon,
            'short_description' => $this->short_description,
            'full_description' => $this->full_description,
            'features' => $this->features,
            'order' => (int) $this->order,
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

