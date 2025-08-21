<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class FollowersController extends Controller
{
    public function index()
    {
        $artists = auth()->user()->followedSellers;
        return view('client.followers.index', compact('artists'));
    }

    public function followedOffers()
    {
        $user = auth()->user();
        $followedSellers = $user->followedSellers()->get();

        $artworks = \App\Models\Artwork::with(['user', 'category'])
            ->whereIn('user_id', $followedSellers->pluck('id'))
            ->where('is_priceless', 0)
            ->where('is_sold', false) // Filter out sold artworks
            ->get();

        // Oferty specjalne tylko dla najhojniejszego darczyńcym
        $specialOffers = [];
        foreach ($followedSellers as $seller) {
            // Znajdź najhojniejszego darczyńcę tego sprzedawcy
            $topDonor = \App\Models\Donation::selectRaw('client_id, SUM(amount) as total')
                ->where('seller_id', $seller->id)
                ->groupBy('client_id')
                ->orderByDesc('total')
                ->first();

            // Jeśli obecny użytkownik jest najhojniejszym darczyńcą
            if ($topDonor && $topDonor->client_id == $user->id) {
                $priceless = \App\Models\Artwork::with(['user', 'category'])
                    ->where('user_id', $seller->id)
                    ->where('is_priceless', 1)
                    ->inRandomOrder()
                    ->first();
                if ($priceless) {
                    $specialOffers[$seller->id] = $priceless;
                }
            }
        }

        $cartArtworks = [];
        if ($user->role === 'user') {
            $cart = $user->cart; // 
            if ($cart) {
                $cart = $cart->load('items'); 
                $cartArtworks = $cart->items->pluck('artwork_id')->toArray();
            }
        }

        return view('client.followers.followed_offers', compact('artworks', 'followedSellers', 'cartArtworks', 'specialOffers'));
    }
}
