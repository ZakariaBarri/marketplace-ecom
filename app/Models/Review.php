<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'reviewer_id', 'reviewed_user_id', 'rating', 'comment'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewedUser()
    {
        return $this->belongsTo(User::class, 'reviewed_user_id');
    }

    //role logic
    /*public static function determineRole($order, $userId)
    {
        if ($userId == $order->buyer_id) {
            return 'buyer';
        } elseif ($userId == $order->seller_id) {
            return 'seller';
        } else {
            throw new \Exception("User not part of this order");
        }
    }*/
}
