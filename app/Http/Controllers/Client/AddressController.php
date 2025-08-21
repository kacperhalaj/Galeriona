<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Wyświetla listę zasobów.
     */
    public function index()
    {
        $addresses = Auth::user()->addresses;
        return view('client.addresses.index', compact('addresses'));
    }

    /**
     * Pokazuje formularz do tworzenia nowego zasobu.
     */
    public function create()
    {
        return view('client.addresses.form');
    }

    /**
     * Zapisuje nowo utworzony zasób w bazie danych.
     */
    public function store(Request $request)
    {
        $request->validate([
            'city' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'house_number' => 'required|string|max:20',
            'apartment_number' => 'nullable|string|max:20',
        ]);

        Auth::user()->addresses()->create($request->all());

        return redirect()->route('client.addresses.index')->with('success', 'Adres został pomyślnie dodany.');
    }

    /**
     * Wyświetla określony zasób.
     */
    public function show(Address $address)
    {
        // Nie używane zgodnie z definicją tras
    }

    /**
     * Pokazuje formularz do edycji określonego zasobu.
     */
    public function edit(Address $address)
    {
        // Upewnij się, że adres należy do zalogowanego użytkownika
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }
        return view('client.addresses.form', compact('address'));
    }

    /**
     * Aktualizuje określony zasób w bazie danych.
     */
    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'city' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'house_number' => 'required|string|max:20',
            'apartment_number' => 'nullable|string|max:20',
        ]);

        $address->update($request->all());

        return redirect()->route('client.addresses.index')->with('success', 'Adres został pomyślnie zaktualizowany.');
    }

    /**
     * Usuwa określony zasób z bazy danych.
     */
    public function destroy(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        return redirect()->route('client.addresses.index')->with('success', 'Adres został pomyślnie usunięty.');
    }
}
