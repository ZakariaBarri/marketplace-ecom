<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'price' => number_format($this->price, 2),
            'status' => $this->status,

            'images' => $this->images->map(function ($img) {
                return asset('storage/' . $img->path);
            }),
            'main_image' =>  optional(
                $this->images->where('is_main', true)->first()
            )->path ? asset('storage/' . $this->images->where('is_main', true)->first()->path) : null,
            //asset('storage/' . $this->images->where('is_main', true)->first()->path) ,

            'condition' => $this->whenLoaded('condition', function () {
                return [
                    'id' => $this->condition->id,
                    'name' => $this->condition->name,
                ];
            }),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),
            'seller' => $this->whenLoaded('seller', function () {
                return [
                    'id' => $this->seller->id,
                    'name' => $this->seller->name,
                    'email' => $this->seller->email,
                ];
            }),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString()
        ];
    }
}
