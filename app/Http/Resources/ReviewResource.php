<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'rating' => $this->rating,
            //'stars' => $this->getStars(), // نجوم
            'comment' => $this->comment,
            'reviewer' => [
                'id' => $this->reviewer->id,
                'name' => $this->reviewer->name,
                //'avatar' => $this->reviewer->avatar_url ?? null,
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            // تاريخ التقييم بصيغة جميلة
            //use Carbon\Carbon;  --> “قبل 3 أيام”
            //'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
            // اختياري: يمكن إضافة order_id أو بيانات الطلب
            'order_id' => $this->order_id,
        ];
    }
}
