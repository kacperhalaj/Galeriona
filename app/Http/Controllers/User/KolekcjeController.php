<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KolekcjeController extends Controller
{
    public function index(Request $request)
    {

        $query = Artwork::query();
        $query->where('is_priceless', 0);
        $query->where('is_sold', false); 

        if ($request->filled('nazwa')) {
            $query->where('title', 'like', '%' . $request->nazwa . '%');
        }

        if ($request->filled('autor')) {
            $query->where('artist', 'like', '%' . $request->autor . '%');
        }

        if ($request->filled('cena_od')) {
            $query->where('price', '>=', $request->cena_od);
        }

        if ($request->filled('cena_do')) {
            $query->where('price', '<=', $request->cena_do);
        }

        if ($request->filled('kategoria')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->kategoria . '%');
            });
        }

        $artworks = $query->paginate(9);
        $categories = Category::all();

        $cartArtworks = [];

        if (Auth::check() && Auth::user()->role === 'user') {
            $cart = Auth::user()->cart;
            if ($cart) {
                $cart = $cart->load('items'); 
                $cartArtworks = $cart->items->pluck('artwork_id')->toArray();
            }
        }

        if ($request->ajax()) {
            return view('components.wyszukiwarka', compact('artworks', 'categories', 'cartArtworks'))->render();
        }

        return view('home', compact('artworks', 'categories', 'cartArtworks'));
    }
}
