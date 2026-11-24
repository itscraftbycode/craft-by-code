<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactInquiryResource extends JsonResource
{
    /**
     * Transform the contact inquiry into an array.
     * IP addresses and internal metadata are filtered based on permission level.
     */
    public function toArray(Request $request): array
    {
        $isAdmin = $request->user()?->role === 'admin';

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'service_interested' => $this->service_interested,
            'budget_range' => $this->budget_range,
            'message' => $this->message,
            'status' => $this->status,
            'ip_address' => $this->when($isAdmin, $this->ip_address),
            'admin_notes' => $this->when($isAdmin, $this->admin_notes),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

