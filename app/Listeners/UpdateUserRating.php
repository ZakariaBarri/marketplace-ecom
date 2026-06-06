<?php

namespace App\Listeners;

use App\Events\ReviewCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateUserRating
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReviewCreated $event)
    {
        $review = $event->review;
        $user = $review->reviewedUser;

        // تحديد إذا كان التقييم للبائع أو المشتري
        $isSeller = $review->reviewer_id === $review->order->buyer_id;

        if ($isSeller) {
            $count = $user->seller_rating_count;
            $avg = $user->seller_rating_avg;

            //$newAvg = (($avg * $count) + $review->rating) / ($count + 1);
            $newAvg = round((($avg * $count) + $review->rating) / ($count + 1), 1);

            $user->update([
                'seller_rating_avg' => $newAvg,
                'seller_rating_count' => $count + 1,
            ]);
        } else {
            $count = $user->buyer_rating_count;
            $avg = $user->buyer_rating_avg;

            $newAvg = (($avg * $count) + $review->rating) / ($count + 1);

            $user->update([
                'buyer_rating_avg' => $newAvg,
                'buyer_rating_count' => $count + 1,
            ]);
        }
    }
}
