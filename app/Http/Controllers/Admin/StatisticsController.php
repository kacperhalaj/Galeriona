<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Artwork;
use App\Models\Sale;

class StatisticsController extends Controller
{
    public function index()
    {
        // Użytkownicy (bez adminów)
        $usersCount = User::where('role', '!=', 'admin')->count();
        $sellersCount = User::where('role', 'seller')->where('role', '!=', 'admin')->count();
        $clientsCount = User::where('role', 'user')->where('role', '!=', 'admin')->count();

        // Dzieła
        $artworksCount = Artwork::count();

        // Sprzedaż
        $totalSales = Sale::count();
        $totalEarned = Sale::sum('price');
        $maxSale = Sale::with('artwork')->orderByDesc('price')->first();

        // Statystyki miesięczne sprzedaży
        $monthlyStatsDb = Sale::selectRaw('DATE_FORMAT(sold_at, "%Y-%m") as month, SUM(price) as total, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyStats = [
            'months' => $monthlyStatsDb->pluck('month')->toArray(),
            'totals' => $monthlyStatsDb->pluck('total')->map(fn($v) => (float)$v)->toArray(),
            'counts' => $monthlyStatsDb->pluck('count')->map(fn($v) => (int)$v)->toArray(),
            'avgs'   => $monthlyStatsDb->map(fn($row) => $row->count ? round($row->total / $row->count, 2) : 0)->toArray(),
        ];

        // Najaktywniejsi sprzedawcy (tylko użytkownicy z 'seller', bez adminów)
        $topSellers = Sale::with('artwork.seller')
            ->get()
            ->filter(
                fn($sale) =>
                $sale->artwork && $sale->artwork->seller && $sale->artwork->seller->role === 'seller'
                    && $sale->artwork->seller->role !== 'admin'
            )
            ->groupBy(function ($sale) {
                $seller = $sale->artwork->seller;
                $first = $seller->first_name ?? '';
                $last = $seller->last_name ?? '';
                $full = trim($first . ' ' . $last);
                return $full !== '' ? $full : $seller->email;
            })
            ->map(fn($g) => $g->count())
            ->sortDesc()
            ->take(6)
            ->toArray();

        // Najpopularniejsze dzieła
        $topArtworks = Sale::with('artwork')
            ->get()
            ->groupBy(fn($sale) => $sale->artwork->title ?? 'Nieznane dzieło')
            ->map(fn($g) => $g->count())
            ->sortDesc()
            ->take(6)
            ->toArray();

        // Najlepsi klienci (tylko users z 'user', bez adminów)
        $topBuyers = Sale::with('user')
            ->get()
            ->filter(
                fn($sale) =>
                $sale->user && $sale->user->role === 'user' && $sale->user->role !== 'admin'
            )
            ->groupBy(function ($sale) {
                $first = $sale->user->first_name ?? '';
                $last = $sale->user->last_name ?? '';
                $full = trim($first . ' ' . $last);
                return $full !== '' ? $full : $sale->user->email;
            })
            ->map(fn($g) => $g->count())
            ->sortDesc()
            ->take(6)
            ->toArray();

        return view('admin.statistics.index', compact(
            'usersCount',
            'sellersCount',
            'clientsCount',
            'artworksCount',
            'totalSales',
            'totalEarned',
            'maxSale',
            'monthlyStats',
            'topSellers',
            'topArtworks',
            'topBuyers'
        ));
    }
}
