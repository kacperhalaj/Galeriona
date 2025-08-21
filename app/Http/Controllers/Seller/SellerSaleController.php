<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerSaleController extends Controller
{
    // Lista sprzedaży sprzedawcy z filtrowaniem i paginacją
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $query = Sale::with(['artwork', 'buyer']);

        // Tylko dzieła sprzedawcy
        $query->whereHas('artwork', function ($q) {
            $q->where('user_id', Auth::id());
        });

        // Filtrowanie po tytule, artyście lub kupującym (username)
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('artwork', function ($q2) use ($keyword) {
                    $q2->where('title', 'like', "%$keyword%")
                        ->orWhere('artist', 'like', "%$keyword%");
                })
                    ->orWhereHas('buyer', function ($q3) use ($keyword) {
                        $q3->where('username', 'like', "%$keyword%");
                    });
            });
        }

        $sales = $query->orderByDesc('sold_at')->paginate(10);
        $sales->appends(['keyword' => $keyword]);

        return view('seller.sales.index', compact('sales', 'keyword'));
    }

    // Szczegóły sprzedaży
    public function show($id)
    {
        $sale = Sale::with(['artwork', 'buyer'])
            ->whereHas('artwork', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->findOrFail($id);

        return view('seller.sales.show', compact('sale'));
    }

    // Usuwanie sprzedaży
    public function destroy($id)
    {
        $sale = Sale::with('artwork')
            ->whereHas('artwork', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->findOrFail($id);

        $sale->delete();

        return redirect()->route('seller.sales.index')->with('success', 'Sprzedaż została usunięta.');
    }
}
