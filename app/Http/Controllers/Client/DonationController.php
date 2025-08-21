<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Donation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    public function index()
    {
        // Pobierz wszystkich użytkowników z rolą 'seller'
        $sellers = \App\Models\User::where('role', 'seller')->get();

        $recentDonations = \App\Models\Donation::where('client_id', auth()->id())
            ->with('seller')
            ->latest()
            ->take(5)
            ->get();

        return view('client.donations.index', compact('sellers', 'recentDonations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $client = Auth::user();
        $seller = User::findOrFail($request->seller_id);
        $amount = $request->amount;

        // Sprawdź saldo klienta
        if ($client->balance < $amount) {
            return redirect()->back()->with('error', 'Nie masz wystarczających środków w portfelu.');
        }

        // Odejmij środki klientowi
        $client->balance -= $amount;
        $client->save();

        // Dodaj środki sprzedawcy
        $seller->balance += $amount;
        $seller->save();

        // Zapisz darowiznę
        Donation::create([
            'client_id' => $client->id,
            'seller_id' => $seller->id,
            'amount' => $amount,
        ]);

        // Komunikat dla klienta
        session()->flash('success', 'Darowizna została przekazana!');

        $description = 'Otrzymano darowiznę od użytkownika: ' . ($client->username ?? $client->name);

        \App\Models\BalanceHistory::create([
            'user_id' => $seller->id,
            'amount' => $amount,
            'type' => 'donation',
            'description' => $description,
        ]);

        return redirect()->route('client.donations.index');
    }
}
