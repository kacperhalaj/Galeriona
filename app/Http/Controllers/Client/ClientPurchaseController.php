<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientPurchaseController extends Controller
{
    // Lista zakupów klienta
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $artist = $request->input('artist');
        $query = Sale::with(['artwork', 'artwork.seller']);

        $query->where('user_id', Auth::id());

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('artwork', function ($q2) use ($keyword) {
                    $q2->where('title', 'like', "%$keyword%")
                        ->orWhere('artist', 'like', "%$keyword%");
                })
                    ->orWhereHas('artwork.seller', function ($q3) use ($keyword) {
                        $q3->where('username', 'like', "%$keyword%");
                    });
            });
        }

        if ($artist) {
            $query->whereHas('artwork', function ($q) use ($artist) {
                $q->where('artist', $artist);
            });
        }

        $purchases = $query->orderByDesc('sold_at')->paginate(10);
        $purchases->appends(['keyword' => $keyword, 'artist' => $artist]);

        // Lista unikalnych artystów
        $artists = Sale::where('user_id', Auth::id())
            ->with('artwork')
            ->get()
            ->pluck('artwork.artist')
            ->unique()
            ->sort()
            ->values();

        return view('client.purchases.index', compact('purchases', 'keyword', 'artist', 'artists'));
    }

    // Szczegóły zakupu
    public function show($id)
    {
        $purchase = Sale::with(['artwork', 'artwork.seller'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('client.purchases.show', compact('purchase'));
    }
}
