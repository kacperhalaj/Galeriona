<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArtworkController extends Controller
{
    // Wyświetla listę dzieł zalogowanego użytkownika
    public function index()
    {
        $artworks = Artwork::where('user_id', Auth::id())->get();
        return view('seller.artworks.index', compact('artworks'));
    }

    // Pokazuje formularz dodawania nowego dzieła
    public function create()
    {
        $categories = Category::all();
        return view('seller.artworks.create', compact('categories'));
    }

    // Zapisuje nowe dzieło do bazy
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'artist' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'depth' => 'nullable|numeric',
            'category_id' => 'nullable|exists:categories,id',
        ]);


        // Ustawienie is_priceless na podstawie checkboxa
        $validated['is_priceless'] = $request->has('is_priceless') ? 1 : 0;

        // Jeśli bezcenne, cena na null
        if ($validated['is_priceless']) {
            $validated['price'] = 1000000;
        }


        $validated['user_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('artworksImage'), $filename);
            $validated['image_path'] = 'artworksImage/' . $filename;
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
        $categories = Category::all();
        return view('seller.artworks.edit', compact('artwork', 'categories'));
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
            'image' => 'nullable|image|max:2048',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        // Ustawienie is_priceless na podstawie checkboxa
        $validated['is_priceless'] = $request->has('is_priceless') ? 1 : 0;

        // Jeśli bezcenne, cena na null
        if ($validated['is_priceless']) {
            $validated['price'] = 1000000;
        }

        // Obsługa uploadu nowego zdjęcia
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('artworksImage'), $filename);
            $validated['image_path'] = 'artworksImage/' . $filename;
        }

        $artwork->update($validated);

        return redirect()->route('seller.artworks.index')->with('success', 'Dzieło zostało zaktualizowane!');
    }

    // Usuwa dzieło z bazy
    public function destroy(Artwork $artwork)
    {
        $this->authorize('delete', $artwork);
        $artwork->delete();

        return redirect()->route('seller.artworks.index')->with('success', 'Dzieło zostało usunięte!');
    }
}
