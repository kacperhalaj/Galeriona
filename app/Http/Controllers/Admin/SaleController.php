<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Artwork;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $query = Sale::with(['artwork', 'user']);

        // Filtrowanie tylko po dzieło sztuki i artysta
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('artwork', function ($q2) use ($keyword) {
                    $q2->where('title', 'like', "%$keyword%")
                        ->orWhere('artist', 'like', "%$keyword%");
                });
            });
        }

        $sales = $query->orderByDesc('sold_at')->paginate(10);
        $sales->appends(['keyword' => $keyword]);

        return view('admin.sales.index', compact('sales', 'keyword'));
    }

    public function create()
    {
        $soldArtworkIds = Sale::pluck('artwork_id')->toArray();
        $artworks = Artwork::whereNotIn('id', $soldArtworkIds)->get();
        $users = User::where('role', 'user')->get();
        $defaultSoldAt = Carbon::now('Europe/Warsaw')->format('Y-m-d\TH:i');
        return view('admin.sales.create', compact('artworks', 'users', 'defaultSoldAt'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'artwork_id' => 'required|exists:artworks,id',
            'user_id' => 'required|exists:users,id',
            'price' => 'required|numeric|min:0',
            'sold_at' => 'required|date',
        ]);

        // rola user
        $user = User::find($validated['user_id']);
        if (!$user || $user->role !== 'user') {
            return back()->withErrors(['user_id' => 'Kupującym może być tylko zwykły użytkownik.'])->withInput();
        }
        // dzielo sprzedane
        if (Sale::where('artwork_id', $validated['artwork_id'])->exists()) {
            return back()->withErrors(['artwork_id' => 'To dzieło sztuki zostało już sprzedane!'])->withInput();
        }

        // Cena z bazy
        $artwork = Artwork::find($validated['artwork_id']);
        $validated['price'] = $artwork->price;

        Sale::create($validated);
        return redirect()->route('admin.sales.index')->with('success', 'Sprzedaż została dodana.');
    }

    public function show(Sale $sale)
    {
        $sale->load(['artwork', 'user']);
        return view('admin.sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $soldArtworkIds = Sale::where('id', '!=', $sale->id)->pluck('artwork_id')->toArray();
        $artworks = Artwork::whereNotIn('id', $soldArtworkIds)
            ->orWhere('id', $sale->artwork_id)
            ->get();
        $users = User::where('role', 'user')->get();
        return view('admin.sales.edit', compact('sale', 'artworks', 'users'));
    }

    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'artwork_id' => 'required|exists:artworks,id',
            'user_id' => 'required|exists:users,id',
            'price' => 'required|numeric|min:0',
            'sold_at' => 'required|date',
        ]);

        // rola user
        $user = User::find($validated['user_id']);
        if (!$user || $user->role !== 'user') {
            return back()->withErrors(['user_id' => 'Kupującym może być tylko zwykły użytkownik.'])->withInput();
        }
        // dzieło nie sprzedane
        $alreadySold = Sale::where('artwork_id', $validated['artwork_id'])
            ->where('id', '!=', $sale->id)
            ->exists();
        if ($alreadySold) {
            return back()->withErrors(['artwork_id' => 'To dzieło sztuki zostało już sprzedane!'])->withInput();
        }

        // Cena zawsze z bazy
        $artwork = Artwork::find($validated['artwork_id']);
        $validated['price'] = $artwork->price;

        $sale->update($validated);
        return redirect()->route('admin.sales.index')->with('success', 'Sprzedaż została zaktualizowana.');
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return redirect()->route('admin.sales.index')->with('success', 'Sprzedaż została usunięta.');
    }
}
