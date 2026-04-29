<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'confirmed' => $this->confirmed,
            'order_items' => OrderItemResource::collection($this->whenLoaded('items')),
            'payment_status' => optional($this->payment)->status ?: 'unpaid',
            'payment_method' => optional($this->payment)->payment_method,
            'ordered_at' => $this->created_at->translatedFormat('l, j F, Y'),
            'table_number' => $this->whenLoaded('tableSession', function () {
                return $this->tableSession?->table?->number;
            }),
        ];
    }
}
