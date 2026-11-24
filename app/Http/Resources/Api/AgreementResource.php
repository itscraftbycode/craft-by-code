<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgreementResource extends JsonResource
{
    /**
     * Transform the agreement resource into an array.
     * Internal private storage paths, raw hashes, and server paths are redacted.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'agreement_number' => $this->agreement_number,
            'title' => $this->title,
            'category' => $this->template?->category ?: 'Bespoke Software',
            'client_name' => $this->client_name,
            'client_company' => $this->client_company,
            'client_email' => $this->client_email,
            'client_phone' => $this->client_phone,
            'client_address' => $this->client_address,
            'contract_value' => (float) $this->contract_value,
            'formatted_value' => $this->getFormattedValue(),
            'currency' => $this->currency,
            'status' => $this->status,
            'scope_of_work' => $this->scope_of_work,
            'payment_terms' => $this->payment_terms,
            'content' => $this->content,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'signed_at' => $this->signed_at?->toIso8601String(),
            'finalized_at' => $this->finalized_at?->toIso8601String(),
            'has_digital_signature' => $this->hasDigitalSignature(),
            'has_estamp' => $this->hasEstamp(),
            'estamp_number' => $this->when($this->hasEstamp(), $this->estamp_number),
            'estamp_state' => $this->when($this->hasEstamp(), $this->estamp_state),
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

