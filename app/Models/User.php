<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\SellerDescription;
use App\Models\Transaction;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\BalanceHistory;

use Illuminate\Database\Eloquent\Relations\HasMany;



class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'google2fa_secret',
        'google2fa_enabled',
        'balance', // Add balance here
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'google2fa_enabled' => 'boolean',
    ];

    // RELACJE

    public function addresses()
    {
        return $this->hasMany(\App\Models\Address::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function cartItems()
    {
        return $this->hasManyThrough(CartItem::class, Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function sellerDescription()
    {
        return $this->hasOne(SellerDescription::class, 'user_id');
    }

    public function artworks()
    {
        return $this->hasMany(\App\Models\Artwork::class, 'user_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'user_id');
    }

    public function followedSellers()
    {
        return $this->belongsToMany(User::class, 'seller_user_follows', 'user_id', 'seller_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'seller_user_follows', 'seller_id', 'user_id');
    }

    // ROLE

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSeller()
    {
        return $this->role === 'seller';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    // ATRUBUTY DODATKOWE

    public function getTotalOrdersAttribute()
    {
        return $this->sales()->count();
    }


    public function getLoyaltyLevelAttribute()
    {
        $orders = $this->total_orders;

        if ($orders >= 20) return 'Platinum';
        if ($orders >= 10) return 'Gold';
        if ($orders >= 5)  return 'Silver';
        if ($orders >= 1)  return 'Bronze';

        return 'Nowy';
    }

    public function balanceHistories(): HasMany
    {
        return $this->hasMany(BalanceHistory::class)->orderBy('created_at', 'desc');
    }


    public function getLoyaltyProgressAttribute()
    {
        $orders = $this->total_orders;

        if ($orders >= 20) return 100;
        if ($orders >= 10) return round(($orders - 10) / 10 * 100);
        if ($orders >= 5)  return round(($orders - 5) / 5 * 100);
        if ($orders >= 1)  return round(($orders - 1) / 4 * 100);

        return round($orders / 1 * 100);
    }
}
