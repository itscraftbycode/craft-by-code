<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the portfolio project resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'client' => $this->client,
            'category' => $this->category,
            'image' => $this->image,
            'short_description' => $this->short_description,
            'challenge' => $this->challenge,
            'solution' => $this->solution,
            'results' => $this->results,
            'technologies' => $this->technologies,
            'live_url' => $this->live_url,
            'github_url' => $this->github_url,
            'featured' => (bool) $this->featured,
            'status' => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

