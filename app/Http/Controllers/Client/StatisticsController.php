<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Sale;

class StatisticsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Suma i liczba zakupów
        $totalSpent = Sale::where('user_id', $user->id)->sum('price');
        $totalPurchases = Sale::where('user_id', $user->id)->count();

        // Najdroższy zakup
        $maxPurchase = Sale::where('user_id', $user->id)
            ->with('artwork')
            ->orderByDesc('price')
            ->first();

        // Miesięczne statystyki
        $monthlyStatsDb = Sale::where('user_id', $user->id)
            ->selectRaw('DATE_FORMAT(sold_at, "%Y-%m") as month, SUM(price) as total, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyStats = [
            'months' => $monthlyStatsDb->pluck('month')->toArray(),
            'totals' => $monthlyStatsDb->pluck('total')->map(fn($v) => (float)$v)->toArray(),
            'counts' => $monthlyStatsDb->pluck('count')->map(fn($v) => (int)$v)->toArray(),
            'avgs'   => $monthlyStatsDb->map(fn($row) => $row->count ? round($row->total / $row->count, 2) : 0)->toArray(),
        ];

        // Top artyści (liczba zakupionych dzieł)
        $topArtists = Sale::where('user_id', $user->id)
            ->with('artwork')
            ->get()
            ->groupBy(fn($sale) => $sale->artwork->artist ?? 'Nieznany')
            ->map(fn($g) => $g->count())
            ->sortDesc()
            ->take(6)
            ->toArray();

        // Pie chart - suma wydatków na artystów
        $artistPie = Sale::where('user_id', $user->id)
            ->with('artwork')
            ->get()
            ->groupBy(fn($sale) => $sale->artwork->artist ?? 'Nieznany')
            ->map(fn($g) => $g->sum('price'))
            ->sortDesc()
            ->take(10)
            ->toArray();

        // Top sprzedawcy (liczba zakupów)
        $topSellers = Sale::where('user_id', $user->id)
            ->with('artwork.seller')
            ->get()
            ->groupBy(fn($sale) => optional($sale->artwork->seller)->username ?? 'Nieznany')
            ->map(fn($g) => $g->count())
            ->sortDesc()
            ->take(6)
            ->toArray();

        return view('client.statistics.index', compact(
            'totalSpent',
            'totalPurchases',
            'maxPurchase',
            'monthlyStats',
            'topArtists',
            'artistPie',
            'topSellers'
        ));
    }
}
