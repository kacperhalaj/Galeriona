<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Artwork;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;

use App\Models\Sale;

use Illuminate\Support\Facades\DB;
use App\Models\BalanceHistory;
use Exception; 

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cart = $user->cart;
        if ($cart) {
            $cart->load('items.artwork');
        }

        $cart = $user->cart()->with('items.artwork')->first();

        $items = $cart ? $cart->items : collect();

        [$promotions, $discounts] = $this->calculatePromotionA($items);

        return view('client.cart.index', [
            'items' => $items,
            'promotions' => $promotions,
            'discounts' => $discounts,
        ]);
    }

    public function add($artworkId)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'user') {
            if (request()->ajax()) {
                return response()->json(['error' => 'Nie masz uprawnień.'], 403);
            }
            return redirect()->route('client.cart.index')->with('error', 'Nie masz uprawnień.');
        }
        $cart = $user->cart()->firstOrCreate(['user_id' => $user->id]);
        $item = $cart->items()->where('artwork_id', $artworkId)->first();
        if ($item) {
            if (request()->ajax()) {
                return response()->json(['error' => 'To dzieło już jest w koszyku.'], 409);
            }
            return redirect()->route('client.cart.index')->with('error', 'To dzieło już jest w koszyku.');
        } else {
            $cart->items()->create([
                'artwork_id' => $artworkId,
                'quantity' => 1,
            ]);
        }
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('client.cart.index')->with('success', 'Dodano do koszyka');
    }

    public function remove($artworkId)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'user') {
            if (request()->ajax()) {
                return response()->json(['error' => 'Nie masz uprawnień.'], 403);
            }
            return redirect()->route('client.cart.index')->with('error', 'Nie masz uprawnień.');
        }
        $cart = $user->cart;
        if ($cart) {
            $item = $cart->items()->where('artwork_id', $artworkId)->first();
            if ($item) $item->delete();
        }
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('client.cart.index')->with('success', 'Usunięto z koszyka');
    }

    public function clear()
    {
        $user = Auth::user();
        $cart = $user->cart;
        if ($cart) $cart->items()->delete();
        return redirect()->route('client.cart.index')->with('success', 'Koszyk wyczyszczony');
    }

    protected function calculatePromotionA($items)
    {
        $promotions = [];
        $discounts = [];

        // Grupowanie dzieł po sprzedawcy
        $bySeller = [];
        foreach ($items as $item) {
            $sellerId = $item->artwork->user_id ?? null;
            if ($sellerId) {
                $bySeller[$sellerId][] = $item;
            }
        }

        // min. 2 dzieła od tego samego sprzedawcy
        foreach ($bySeller as $sellerItems) {
            if (count($sellerItems) >= 2) {
                foreach ($sellerItems as $item) {
                    $discounts[$item->id] = $item->artwork->price * 0.10; // 10% zniżki
                }
                $promotions[] = '10% zniżki na dzieła od tego samego sprzedawcy!';
            }
        }

        return [$promotions, $discounts];
    }

    public function checkoutForm()
    {
        $user = Auth::user();
        $cart = $user->cart->with('items.artwork')->first();
        $items = $cart ? $cart->items : collect();
        $addresses = $user->addresses;

        if ($items->isEmpty()) {
            return redirect()->route('client.cart.index')->with('error', 'Koszyk jest pusty!');
        }

        // promocje i rabaty na produkty
        [$promotions, $discounts] = $this->calculatePromotionA($items);

        $sum = 0;
        $totalDiscount = 0;

        foreach ($items as $item) {
            $price = $item->artwork->price;
            $discount = $discounts[$item->id] ?? 0;
            $finalPrice = $price - $discount;

            $sum += $price * $item->quantity;
            $totalDiscount += $discount * $item->quantity;
        }

        return view('client.cart.checkout', [
            'items' => $items,
            'addresses' => $addresses,
            'promotions' => $promotions,
            'discounts' => $discounts,
            'sum' => $sum,
            'totalDiscount' => $totalDiscount,
        ]);
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();
        $cart = $user->cart->with('items.artwork.seller')->first();
        $items = $cart ? $cart->items : collect();

        if ($items->isEmpty()) {
            return redirect()->route('client.cart.index')->with('error', 'Koszyk jest pusty!');
        }

        $request->validate([
            'address_id' => 'required|exists:addresses,id'
        ]);

        [$promotions, $discounts] = $this->calculatePromotionA($items);


        $sumAfterProductDiscounts = 0;
        foreach ($items as $item) {
            $price = $item->artwork->price;
            $discount = $discounts[$item->id] ?? 0;
            $finalPrice = $price - $discount;
            $sumAfterProductDiscounts += $finalPrice * $item->quantity;
        }

        $total = $sumAfterProductDiscounts;

        // zamówienie
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $total,
            'status' => 'pending'
        ]);


        $totalOrderPrice = 0;

        foreach ($items as $item) {
            $price = $item->artwork->price;
            $discount = $discounts[$item->id] ?? 0;
            $finalPrice = $price - $discount;


            OrderItem::create([
                'order_id' => $order->id,
                'artwork_id' => $item->artwork_id,
                'quantity' => $item->quantity,
                'price' => $finalPrice
            ]);
            $totalOrderPrice += $finalPrice * $item->quantity;
        }

        if ($user->balance < $totalOrderPrice) {
            return redirect()->route('client.cart.checkout.form')->with('error', 'Niewystarczające środki na koncie.');
        }

        DB::beginTransaction();
        try {
            $user->balance -= $totalOrderPrice;
            $user->save();

            // Zapis historii dla kupującego
            BalanceHistory::create([
                'user_id' => $user->id,
                'amount' => -$totalOrderPrice, // Kwota ujemna dla zakupu
                'type' => 'purchase',
                'description' => 'Zakup dzieł sztuki (Zamówienie w trakcie tworzenia)'

            ]);

            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => 0,
                'status' => 'pending'
            ]);


        $cart->items()->delete();

            $currentOrderTotal = 0;

            foreach ($items as $item) {
                $artwork = $item->artwork;
                $price = $artwork->price;
                $discount = $discounts[$item->id] ?? 0;
                $finalPriceForItem = $price - $discount;
                $itemTotal = $finalPriceForItem * $item->quantity;
                $currentOrderTotal += $itemTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'artwork_id' => $artwork->id,
                    'quantity' => $item->quantity,
                    'price' => $finalPriceForItem
                ]);

                \App\Models\Sale::create([
                    'user_id'    => $user->id,
                    'artwork_id' => $artwork->id,
                    'price'      => $finalPriceForItem,
                    'sold_at'    => now(),
                ]);

                $seller = $artwork->seller;
                if ($seller) {
                    $seller->balance += $itemTotal;
                    $seller->save();

                    // Zapis historii dla sprzedającego
                    BalanceHistory::create([
                        'user_id' => $seller->id,
                        'amount' => $itemTotal, // Kwota dodatnia dla sprzedaży
                        'type' => 'sale_income',
                        'description' => 'Sprzedaż dzieła: ' . $artwork->title . ' (Zamówienie #' . $order->id . ')'
                    ]);
                }
            }

            $order->update(['total_price' => $currentOrderTotal]);

            // Aktualizacja opisu historii dla kupującego o ID zamówienia
            $purchaseHistory = BalanceHistory::where('user_id', $user->id)
                                ->where('type', 'purchase')
                                ->where('description', 'Zakup dzieł sztuki (Zamówienie w trakcie tworzenia)')
                                ->latest()
                                ->first();
            if ($purchaseHistory) {
                $purchaseHistory->description = 'Zakup dzieł sztuki (Zamówienie #' . $order->id . ')';
                $purchaseHistory->save();
            }

            // Oznacz dzieła jako sprzedane
            foreach ($items as $item) {
                $artwork = $item->artwork;
                if ($artwork) {
                    $artwork->is_sold = true;
                    $artwork->save();
                }
            }

            $cart->items()->delete();

            DB::commit();


            return redirect()->route('client.orders.show', $order->id)->with('success', 'Zamówienie zostało złożone!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Błąd podczas składania zamówienia: ' . $e->getMessage());
            return redirect()->route('client.cart.checkout.form')->with('error', 'Wystąpił błąd podczas składania zamówienia. Spróbuj ponownie.');
        }
    }
}
