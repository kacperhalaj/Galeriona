<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArtworkController extends Controller
{
    // Wyświetla listę dzieł zalogowanego użytkownika
    public function index()
    {
        $artworks = \App\Models\Artwork::with('user')->get();
        return view('admin.artworks.index', compact('artworks'));
    }

    // Pokazuje formularz dodawania nowego dzieła
    public function create()
    {
        $sellers = \App\Models\User::where('role', 'seller')->get();
        return view('admin.artworks.create', compact('sellers'));
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
        ]);

        // Ustawienie is_priceless na podstawie checkboxa
        $validated['is_priceless'] = $request->has('is_priceless') ? 1 : 0;

        // Jeśli bezcenne, cena na null
        if ($validated['is_priceless']) {
            $validated['price'] = 1000000;
        }
        $validated['user_id'] = $request->input('user_id'); // <-- poprawka

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('artworksImage'), $filename);
            $validated['image_path'] = 'artworksImage/' . $filename;
        }

        Artwork::create($validated);

        return redirect()->route('admin.artworks.index')->with('success', 'Dzieło zostało dodane!');
    }

    // Wyświetla szczegóły wybranego dzieła
    public function show(Artwork $artwork)
    {
        return view('admin.artworks.show', compact('artwork'));
    }

    // Pokazuje formularz edycji dzieła
    public function edit(Artwork $artwork)
    {
        $sellers = \App\Models\User::where('role', 'seller')->get();
        return view('admin.artworks.edit', compact('artwork', 'sellers'));    }

    // Aktualizuje dane dzieła w bazie
    public function update(Request $request, Artwork $artwork)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'artist' => 'nullable|string|max:255',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'depth' => 'nullable|numeric',
            'image' => 'nullable|image|max:2048',
        ]);
        // Ustawienie is_priceless na podstawie checkboxa
        $validated['is_priceless'] = $request->has('is_priceless') ? 1 : 0;
        // Jeśli bezcenne, cena na null
        if ($validated['is_priceless']) {
            $validated['price'] = 1000000;
        }
        $validated['user_id'] = $request->input('user_id'); // <-- poprawka

        // Obsługa uploadu nowego zdjęcia
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('artworksImage'), $filename);
            $validated['image_path'] = 'artworksImage/' . $filename;
        }
        $artwork->update($validated);

        return redirect()->route('admin.artworks.index')->with('success', 'Dzieło zostało zaktualizowane!');
    }

    // Usuwa dzieło z bazy
    public function destroy(Artwork $artwork)
    {
        $artwork->delete();

        return redirect()->route('admin.artworks.index')->with('success', 'Dzieło zostało usunięte!');
    }
}
