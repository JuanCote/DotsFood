<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'items', 'city_id',
        'userName', 'userPhone', 'address',
        'paymentUrl'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
