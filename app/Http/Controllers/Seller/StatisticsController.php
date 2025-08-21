<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Artwork;
use App\Models\Sale;
use App\Models\Donation;

class StatisticsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $artworkIds = Artwork::where('user_id', $user->id)->pluck('id');

        $totalEarned = Sale::whereIn('artwork_id', $artworkIds)->sum('price');
        $totalSales = Sale::whereIn('artwork_id', $artworkIds)->count();

        $maxSale = Sale::whereIn('artwork_id', $artworkIds)
            ->with('artwork')
            ->orderByDesc('price')
            ->first();

        $monthlyStatsDb = Sale::whereIn('artwork_id', $artworkIds)
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

        $topArtworks = Sale::whereIn('artwork_id', $artworkIds)
            ->with('artwork')
            ->get()
            ->groupBy(fn($sale) => $sale->artwork->title ?? 'Nieznane dzieło')
            ->map(fn($g) => $g->count())
            ->sortDesc()
            ->take(6)
            ->toArray();

        $artworkPie = Sale::whereIn('artwork_id', $artworkIds)
            ->with('artwork')
            ->get()
            ->groupBy(fn($sale) => $sale->artwork->title ?? 'Nieznane dzieło')
            ->map(fn($g) => $g->sum('price'))
            ->sortDesc()
            ->take(10)
            ->toArray();

        $topBuyers = Sale::whereIn('artwork_id', $artworkIds)
            ->with('user')
            ->get()
            ->groupBy(function ($sale) {
                if ($sale->user) {
                    $first = $sale->user->first_name ?? '';
                    $last = $sale->user->last_name ?? '';
                    $full = trim($first . ' ' . $last);
                    return $full !== '' ? $full : $sale->user->email;
                }
                return 'Nieznany klient';
            })
            ->map(fn($g) => $g->count())
            ->sortDesc()
            ->take(6)
            ->toArray();

        $topDonor = Donation::where('seller_id', Auth::id())
            ->selectRaw('client_id, SUM(amount) as total')
            ->groupBy('client_id')
            ->orderByDesc('total')
            ->with('client')
            ->first();

        return view('seller.statistics.index', compact(
            'totalEarned',
            'totalSales',
            'maxSale',
            'monthlyStats',
            'topArtworks',
            'artworkPie',
            'topBuyers',
            'topDonor'
        ));
    }
}
