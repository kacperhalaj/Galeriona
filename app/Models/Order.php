<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'status'
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function artworks()
    {
        return $this->belongsToMany(Artwork::class, 'order_items')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    public static function getStatusOptions()
    {
        return [
            'pending' => 'Oczekujące',
            'processing' => 'W realizacji',
            'shipped' => 'Wysłane',
            'delivered' => 'Dostarczone',
            'cancelled' => 'Anulowane'
        ];
    }

    public function getStatusLabelAttribute()
    {
        $statuses = self::getStatusOptions();
        return $statuses[$this->status] ?? $this->status;
    }
}
