<?php

namespace App\Http\Resources\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileStatsResource extends JsonResource
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
            'total_sales' => $this['total_sales'] ?? 0,
            'total_purchases' => $this['total_purchases'] ?? 0,

            'ratings' => [
                'seller' => [
                    'avg' => $this['seller_rating_count'] > 0
                        ? round( $this['seller_rating_avg'], 1)
                        : null,
                    'count' => $this['seller_rating_count'] ?? 0,
                ],

                'buyer' => [
                    'avg' => $this['buyer_rating_count'] > 0
                        ? round($this['buyer_rating_avg'], 1)
                        : null,
                    'count' => $this['buyer_rating_count'] ?? 0,
                ],
            ],
        ];
    }
}
