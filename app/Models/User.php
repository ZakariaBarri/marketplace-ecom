<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    //use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'seller_rating_avg',
        'seller_rating_count',
        'buyer_rating_avg',
        'buyer_rating_count'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    // كمشتري
    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    // كبائع
    public function sales()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function addresses()
    {
        return $this->hasMany(Addresse::class, 'buyer_id');
    }

    public function reviewsAsReviewer()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }
    public function reviewsAsReviewed()
    {
        return $this->hasMany(Review::class, 'reviewed_user_id');
    }
}
