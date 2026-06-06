<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isIndex = $request->routeIs('admin.products.index');

        $mainImage = $this->images
            ->firstWhere('is_main', true)
            ?? $this->images->first();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'status' => $this->status,

            'main_image' => $mainImage
                ? asset('storage/' . $mainImage->path)
                : null,

            // 📌 detail (full version)
            $this->mergeWhen(!$isIndex, [
                'description' => $this->description,

                'images' => $this->images->map(fn($img) => [
                    'id' => $img->id,
                    'url' => asset('storage/' . $img->path),
                    'is_main' => (bool) $img->is_main,
                ]),

                'size' => $this->whenLoaded('size', fn() => $this->simpleRelation($this->size)),
            ]),

            'category' => $this->whenLoaded('category', fn() => $this->simpleRelation($this->category)),
            'condition' => $this->whenLoaded('condition', fn() => $this->simpleRelation($this->condition)),

            'seller' => $this->whenLoaded('seller', fn() => [
                'id' => $this->seller->id,
                'name' => $this->seller->name,
                'email' => $this->seller->email,
                'seller_rating_avg' => $this->seller->seller_rating_avg,
                'seller_rating_count' => $this->seller->seller_rating_count,
            ]),

            // 📊 Admin analytics
            //'views_count' => $this->views_count ?? 0,
            //'favorites_count' => $this->favorites_count ?? 0,
            'orders_count' => $this->orders_count,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    private function simpleRelation($relation)
    {
        return [
            'id' => $relation->id,
            'name' => $relation->name,
        ];
    }
}
