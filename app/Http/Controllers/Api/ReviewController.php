<?php

namespace App\Http\Controllers\Api;

use App\Events\ReviewCreated;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Traits\ApiResponse;

class ReviewController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reviews = Review::with(['reviewer', 'reviewedUser'])
            ->latest()
            ->paginate(10);

        return $this->success(ReviewResource::collection($reviews));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReviewRequest $request)
    {
        $user = auth()->user();
        $order = Order::findOrFail($request->order_id);

        $this->authorize('create', [Review::class, $order]);

        // تحديد من سيتم تقييمه
        $reviewedUserId = $user->id === $order->buyer_id
            ? $order->seller_id
            : $order->buyer_id;

        $review = Review::create([
            'order_id' => $order->id,
            'reviewer_id' => $user->id,
            'reviewed_user_id' => $reviewedUserId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Dispatch Event لتحديث تقييم المستخدم
        ReviewCreated::dispatch($review);

        return $this->success(new ReviewResource($review), 'Review added successfully', 201);
    }


    // استرجاع التقييمات التي حصل عليها مستخدم معين
    public function userReviews($userId)
    {
        $reviews = Review::with('reviewer')
            ->where('reviewed_user_id', $userId)
            ->latest()
            ->paginate(10);

        return $this->success(ReviewResource::collection($reviews));
    }

    // تقييمات كبائع
    public function sellerReviews($userId)
    {
        $reviews = Review::where('reviewed_user_id', $userId)
            ->whereHas('order', function ($q) use ($userId) {
                $q->where('seller_id', $userId);
            })
            ->with('reviewer')
            ->latest()
            ->paginate(10);

        return $this->success(ReviewResource::collection($reviews));
    }

    // تقييمات كمشتري
    public function buyerReviews($userId)
    {
        $reviews = Review::where('reviewed_user_id', $userId)
            ->whereHas('order', function ($q) use ($userId) {
                $q->where('buyer_id', $userId);
            })
            ->with('reviewer')
            ->latest()
            ->paginate(10);

        return $this->success(ReviewResource::collection($reviews));
    }

    // تقييمات قمت بكتابتها شخصيًا
    public function myReviews()
    {
        $reviews = Review::with('reviewedUser')
            ->where('reviewer_id', auth()->id())
            ->latest()
            ->paginate(10);

        return $this->success(ReviewResource::collection($reviews));
    }

    /*
    public function userRating($userId)
    {
        return Cache::remember("user_rating_$userId", 300, function () use ($userId) {

            $sellerStats = Review::whereHas('order', function ($q) use ($userId) {
                $q->where('seller_id', $userId);
            })
                ->whereColumn('reviews.reviewer_id', 'orders.buyer_id')
                ->selectRaw('AVG(rating) as avg, COUNT(*) as count')
                ->first();

            $buyerStats = Review::whereHas('order', function ($q) use ($userId) {
                $q->where('buyer_id', $userId);
            })
                ->whereColumn('reviews.reviewer_id', 'orders.seller_id')
                ->selectRaw('AVG(rating) as avg, COUNT(*) as count')
                ->first();

            return [
                'seller' => [
                    'avg' => round($sellerStats->avg ?? 0, 1),
                    'count' => $sellerStats->count ?? 0,
                ],
                'buyer' => [
                    'avg' => round($buyerStats->avg ?? 0, 1),
                    'count' => $buyerStats->count ?? 0,
                ],
            ];
        });
    }
    */
    //Top Sellers:
    //- User A ⭐ 4.9 (230 reviews)
    //- User B ⭐ 4.8 (180 reviews)

    /*public function destroy(Review $review)
    {
        $this->authorize('delete', $review);

        $review->delete();

        return $this->success(null, 'Review deleted successfully');
    }*/

    /**
     * Display the specified resource.
     */
    /*public function show(string $id)
    {
        $review = Review::with('reviewer', 'reviewedUser')->findOrFail($id);
        return $this->success(new ReviewResource($review));
    }*/

    /**
     * Update the specified resource in storage.
     */
    /*public function update(Request $request, Review $review)
    {
        $this->authorize('update', $review);

        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $review->update([
            'comment' => $request->comment,
        ]);

        return $this->success(new ReviewResource($review), 'Review updated successfully');
    }*/
}
