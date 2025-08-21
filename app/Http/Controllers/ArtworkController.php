<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArtworkController extends Controller
{
    public function listPublic(Request $request)
    {
        $query = Artwork::query();

        // Tylko dzieła nie-bezcenne i niesprzedane
        $query->where('is_priceless', 0);
        $query->where('is_sold', false);

        if ($request->filled('keyword')) {
            $keyword = $request->get('keyword');
            $query->where('title', 'like', "%{$keyword}%");
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->get('category'));
        }

        $artworks = $query->paginate(10);
        $categories = Category::all();


        $cartArtworks = [];
        if (Auth::check() && Auth::user()->role === 'user') {

            $cart = Auth::user()->cart;
            if ($cart) {
                $cart = $cart->load('items');
                $cartArtworks = $cart->items->pluck('artwork_id')->toArray();
            }
        }

        return view('artworks.index', [
            'artworks' => $artworks,
            'cartArtworks' => $cartArtworks,
            'categories' => $categories,
        ]);

    }


    public function index()
    {
        $artworks = Artwork::where('user_id', Auth::id())
            ->where('is_priceless', 0)
            ->get();
        return view('seller.artworks.index', compact('artworks'));
    }

    // Pokazuje formularz dodawania nowego dzieła
    public function create()
    {
        return view('seller.artworks.create');
    }

    // Zapisuje nowe dzieło do bazy
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'artist' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048', // max 2MB
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'depth' => 'nullable|numeric',
        ]);

        $validated['is_priceless'] = $request->has('is_priceless') ? 1 : 0;

        // Jeśli bezcenne, cena na null
        if ($validated['is_priceless']) {
            $validated['price'] = null;
        }

        $validated['user_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('artworksImage'), $filename);
            $validated['image_path'] = 'public/artworksImage/' . $filename;
        }

        Artwork::create($validated);

        return redirect()->route('seller.artworks.index')->with('success', 'Dzieło zostało dodane!');
    }

    // Wyświetla szczegóły wybranego dzieła
    public function show(Artwork $artwork)
    {
        $this->authorize('view', $artwork);
        return view('seller.artworks.show', compact('artwork'));
    }

    // Pokazuje formularz edycji dzieła
    public function edit(Artwork $artwork)
    {
        $this->authorize('update', $artwork);
        return view('seller.artworks.edit', compact('artwork'));
    }

    // Aktualizuje dane dzieła w bazie
    public function update(Request $request, Artwork $artwork)
    {
        $this->authorize('update', $artwork);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'artist' => 'nullable|string|max:255',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'depth' => 'nullable|numeric',
            'image' => 'nullable|image|max:2048', // max 2MB
        ]);
        // Ustawienie is_priceless na podstawie checkboxa
        $validated['is_priceless'] = $request->has('is_priceless') ? 1 : 0;

        // Jeśli bezcenne, cena na null
        if ($validated['is_priceless']) {
            $validated['price'] = 0;
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            // Usuń stare zdjęcie, jeśli istnieje
            if ($artwork->image_path && Storage::disk('public')->exists($artwork->image_path)) {
                Storage::disk('public')->delete($artwork->image_path);
            }
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('artworksImage'), $filename);
            $validated['image_path'] = 'public/artworksImage/' . $filename;
        }

        $artwork->update($validated);

        return redirect()->route('seller.artworks.index')->with('success', 'Dzieło zostało zaktualizowane!');
    }

    // Usuwa dzieło z bazy
    public function destroy(Artwork $artwork)
    {
        $this->authorize('delete', $artwork);

        // Usuń zdjęcie z dysku, jeśli istnieje
        if ($artwork->image_path && Storage::disk('public')->exists($artwork->image_path)) {
            Storage::disk('public')->delete($artwork->image_path);
        }

        $artwork->delete();

        return redirect()->route('seller.artworks.index')->with('success', 'Dzieło zostało usunięte!');
    }
}
