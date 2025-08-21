<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'orderItems.artwork']);

        // Filtrowanie
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user_search . '%')
                    ->orWhere('email', 'like', '%' . $request->user_search . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        $statusOptions = Order::getStatusOptions();

        return view('admin.orders.index', compact('orders', 'statusOptions'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.artwork']);
        return view('admin.orders.show', compact('order'));
    }

    public function create()
    {
        $users = User::where('role', '!=', 'admin')->get();
        $artworks = Artwork::all();
        $statusOptions = Order::getStatusOptions();

        return view('admin.orders.create', compact('users', 'artworks', 'statusOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:' . implode(',', array_keys(Order::getStatusOptions())),
            'items' => 'required|array|min:1',
            'items.*.artwork_id' => 'required|exists:artworks,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ]);

        DB::transaction(function () use ($request) {
            $totalPrice = 0;
            foreach ($request->items as $item) {
                $totalPrice += $item['price'] * $item['quantity'];
            }

            $order = Order::create([
                'user_id' => $request->user_id,
                'total_price' => $totalPrice,
                'status' => $request->status
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'artwork_id' => $item['artwork_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            }
        });

        return redirect()->route('admin.orders.index')
            ->with('success', 'Zamówienie zostało utworzone pomyślnie.');
    }

    public function edit(Order $order)
    {
        $order->load(['orderItems.artwork']);
        $users = User::where('role', '!=', 'admin')->get();
        $artworks = Artwork::all();
        $statusOptions = Order::getStatusOptions();

        return view('admin.orders.edit', compact('order', 'users', 'artworks', 'statusOptions'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:' . implode(',', array_keys(Order::getStatusOptions())),
            'items' => 'required|array|min:1',
            'items.*.artwork_id' => 'required|exists:artworks,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ]);

        DB::transaction(function () use ($request, $order) {

            $totalPrice = 0;
            foreach ($request->items as $item) {
                $totalPrice += $item['price'] * $item['quantity'];
            }


            $order->update([
                'user_id' => $request->user_id,
                'total_price' => $totalPrice,
                'status' => $request->status
            ]);


            $order->orderItems()->delete();

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'artwork_id' => $item['artwork_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            }
        });

        return redirect()->route('admin.orders.index')
            ->with('success', 'Zamówienie zostało zaktualizowane pomyślnie.');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Zamówienie zostało usunięte pomyślnie.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Order::getStatusOptions()))
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status zamówienia został zaktualizowany.',
            'new_status' => $order->status_label
        ]);
    }
}
