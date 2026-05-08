<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TableSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'token'          => $this->token,
            'customer_name' => $this->customer_name,
            'status'         => $this->status,
            'seated_at'      => $this->seated_at?->toISOString(),
            'checked_out_at' => $this->checked_out_at?->toISOString(),
            'created_at'     => $this->created_at?->toISOString(),
            'updated_at'     => $this->updated_at?->toISOString(),

            // Relationships — only loaded when eager-loaded
            'table'  => new TableResource($this->whenLoaded('table')),
            'orders' => OrderResource::collection($this->whenLoaded('orders')),
            'invoice'=> new InvoiceResource($this->whenLoaded('invoice')),

            // Computed helpers
            'is_active' => $this->status === 'active',
        ];
    }
}