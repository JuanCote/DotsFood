<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $casts = [
        'items' => 'array',
    ];


    protected $fillable = [
        'user_id', 'items', 'city_id',
        'userName', 'userPhone', 'address',
        'payment_type', 'company_id', 'delivery_type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
