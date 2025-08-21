<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SellerDescription;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    // Lista sprzedawców z opisem i paginacją
    public function sellersIndex()
    {
        $sellers = User::where('role', 'seller')->with('sellerDescription')->paginate(10);

        return view('guests.sellersIndex', compact('sellers'));
    }

    // Publiczny profil sprzedawcy
    public function publicProfile($id)
    {
        $seller = User::where('role', 'seller')
            ->with([
                'sellerDescription',
                'artworks' => function ($query) {
                    $query->where('is_sold', false)->where('is_priceless', 0);
                }
            ])
            ->findOrFail($id);

        return view('guests.sellerProfile', compact('seller'));
    }

    // Publiczna lista dzieł sprzedawcy (jeśli masz relację artworks)
    public function publicArtworks($id)
    {
        $seller = User::where('role', 'seller')
            ->with([
                'sellerDescription',
                'artworks' => function ($query) {
                    $query->where('is_sold', false)->where('is_priceless', 0);
                }
            ])
            ->findOrFail($id);

        return view('guests.sellerArtworks', compact('seller'));
    }
}
