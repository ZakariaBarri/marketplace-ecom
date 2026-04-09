<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'city' => $this->city,
            'address' => $this->address,
            'contact_phone' => $this->contact_phone,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
