<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city',
        'postal_code',
        'street',
        'house_number',
        'apartment_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
