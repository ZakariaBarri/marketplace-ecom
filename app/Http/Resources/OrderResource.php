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
        //return parent::toArray($request);

        $myReview = $this->reviews->firstWhere('reviewer_id', auth()->id());

        return [
            'id' => $this->id,
            'status' => $this->status,
            'price' => $this->price,

            'product' => new ProductResource($this->whenLoaded('product')),

            'buyer' => $this->whenLoaded('buyer', function () {
                return [
                    'id' => $this->buyer->id,
                    'name' => $this->buyer->name,
                ];
            }),

            'seller' => $this->whenLoaded('seller', function () {
                return [
                    'id' => $this->seller->id,
                    'name' => $this->seller->name,
                ];
            }),

            'address' => $this->whenLoaded('addresse', function () {
                return [
                    'id' => $this->addresse->id,
                    'city' => $this->addresse->city,
                    'address' => $this->addresse->address,
                    'contact_phone' => $this->addresse->contact_phone,
                ];
            }),

            'created_at' => $this->created_at,

            'my_review' => $myReview ? [
                'rating' => $myReview->rating,
                'comment' => $myReview->comment,
                'created_at' => $myReview->created_at,
            ] : null,

            'can_review' => $this->status === 'delivered' && !$myReview,
            
        ];

        /*
        return [
            'id' => $this->id,
            'status' => $this->status,

            'product' => [
                'id' => $this->product->id,
                'title' => $this->product->title,
            ],

            'actions' => [
                'can_accept' => Gate::allows('accept', $this->resource),
                'can_reject' => Gate::allows('reject', $this->resource),
                'can_cancel' => Gate::allows('cancel', $this->resource),
                'can_ship' => Gate::allows('ship', $this->resource),
                'can_deliver' => Gate::allows('deliver', $this->resource),
            ]
        ];
        */
    }
}
