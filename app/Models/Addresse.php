<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addresse extends Model
{
    use HasFactory;
    protected $fillable = ['city','address','phone'];

    public function orders(){
        return $this->hasMany(Order::class);
    }
}
