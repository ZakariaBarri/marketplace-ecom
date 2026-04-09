<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addresse extends Model
{
    use HasFactory;
    protected $fillable = ['city','address','contact_phone','buyer_id'];

    public function orders(){
        return $this->hasMany(Order::class);
    }

    public function buyer(){
        return $this->belongsTo(User::class,'buyer_id');
    }
}
