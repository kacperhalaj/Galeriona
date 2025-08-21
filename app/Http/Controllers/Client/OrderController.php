<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\BalanceHistory;

class OrderController extends Controller
{
    // Złożenie zamówienia
    public function store(Request $request)
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('client.cart.index')->with('error', 'Koszyk jest pusty!');
        }

        $artworks = Artwork::whereIn('id', array_keys($cart))->get();
        if ($artworks->isEmpty()) {
            return redirect()->route('client.cart.index')->with('error', 'Brak dostępnych dzieł w koszyku.');
        }

        DB::transaction(function () use ($artworks) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_price' => $artworks->sum('price'),
                'status' => 'pending',
            ]);
            foreach ($artworks as $artwork) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'artwork_id' => $artwork->id,
                    'quantity'   => 1,
                    'price'      => $artwork->price,
                ]);
                $artwork->delete();
            }
            session()->forget('cart');
        });

        return redirect()->route('client.orders.index')->with('success', 'Zamówienie zostało złożone!');
    }

    // Lista zamówień klienta
    public function index()
    {
        $orders = Order::with('orderItems.artwork')->where('user_id', Auth::id())->orderByDesc('created_at')->paginate(10);
        return view('client.orders.index', compact('orders'));
    }

    // Szczegóły zamówienia
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        $order->load('orderItems.artwork');
        return view('client.orders.show', compact('order'));
    }

    public function buy(Request $request, $artworkId)
    {
        $user = Auth::user();
        $artwork = Artwork::findOrFail($artworkId);

        // Sprawdź czy dzieło nie jest już kupione
        if (!$artwork->is_available) {
            return redirect()->back()->with('error', 'To dzieło nie jest już dostępne.');
        }

        // Sprawdź czy użytkownik ma wystarczające środki
        if ($user->balance < $artwork->price) {
            return redirect()->back()->with('error', 'Nie masz wystarczających środków.');
        }

        $seller = User::findOrFail($artwork->user_id);

        DB::beginTransaction();
        try {
            // Odejmij środki kupującemu
            $user->balance -= $artwork->price;
            $user->save();

            // Dodaj środki sprzedawcy
            $seller->balance += $artwork->price;
            $seller->save();

            // Historia salda kupującego
            BalanceHistory::create([
                'user_id' => $user->id,
                'amount' => -$artwork->price,
                'type' => 'purchase',
                'description' => 'Zakup dzieła: ' . $artwork->title,
            ]);

            // Historia salda sprzedawcy
            BalanceHistory::create([
                'user_id' => $seller->id,
                'amount' => $artwork->price,
                'type' => 'sale',
                'description' => 'Sprzedaż dzieła: ' . $artwork->title . ' dla użytkownika: ' . ($user->username ?? $user->name),
            ]);

            // Oznacz dzieło jako niedostępne lub usuń z bazy
            $artwork->delete();

            DB::commit();

            return redirect()->route('client.purchases.index')->with('success', 'Zakup zakończony sukcesem!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Wystąpił błąd podczas zakupu: ' . $e->getMessage());
        }
    }
}
