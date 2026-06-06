<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,

            'type' => $this->data['type'] ?? $this->getType(),

            'message' => $this->data['message'] ?? null,
            'order_id' => $this->data['order_id'] ?? null,

            'buyer' => $this->data['buyer'] ?? null,

            'is_read' => $this->read_at !== null,
            'read_at' => $this->read_at,

            'created_at' => $this->created_at,
            'time_ago' => $this->created_at->diffForHumans(),
        ];
    }

    // 🔥 تحويل type من class إلى string بسيط
    private function getType()
    {
        return match ($this->type) {
            'App\\Notifications\\OrderCreatedNotification' => 'order_created',
            default => 'unknown',
        };
    }
}
