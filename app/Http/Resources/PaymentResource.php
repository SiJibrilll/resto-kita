<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'grand_total'    => $this->whenNotNull($this->grand_total),
            'payment_method' => $this->whenNotNull($this->payment_method),
            'status'         => $this->whenNotNull($this->status),
            'snap_token'     => $this->whenNotNull($this->snap_token),
            'customer_name'  => $this->whenNotNull($this->customer_name),
            'invoice_id'     => $this->whenNotNull($this->invoice_id),
            // 'created_at'     => $this->created_at?->toISOString(),
            // 'updated_at'     => $this->updated_at?->toISOString(),
        ];
    }
}