<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressState extends Model
{
    use HasFactory;

    protected $table = 'addresses_states';

    protected $fillable = [
        'state', 'city_id', 'street',
        'house', 'flat', 'stage', 'note',
        'title', 'user_id', 'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
