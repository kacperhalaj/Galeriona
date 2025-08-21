<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\BalanceHistory;

class ClientWalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $balance = $user->balance;
        $balanceHistories = $user->balanceHistories()->orderBy('created_at', 'desc')->take(10)->get(); // Pobierz 10 najnowszych
        return view('client.wallet.index', compact('user', 'balance', 'balanceHistories'));
    }

    /**
     * Wyświetla formularz doładowania portfela.
     */
    public function showTopUpForm()
    {
        return view('client.wallet.topup');
    }

    /**
     * Przetwarza żądanie doładowania portfela.
     */
    public function processTopUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:blik,card,paysafecard,transfer',
        ]);

        if ($validator->fails()) {
            return redirect()->route('client.wallet.topup.form')
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        $amount = (float) $request->input('amount');

        try {
            $user->balance += $amount;
            $user->save();

            BalanceHistory::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'deposit',
                'description' => 'Doładowanie portfela: ' . $request->input('payment_method'),
            ]);

            return redirect()->route('client.wallet.index')->with('success', 'Saldo zostało pomyślnie doładowane o kwotę ' . number_format($amount, 2, ',', ' ') . ' PLN.');
        } catch (Exception $e) {
            Log::error('Błąd podczas doładowywania portfela: ' . $e->getMessage());
            return redirect()->route('client.wallet.topup.form')->with('error', 'Wystąpił błąd podczas próby doładowania salda. Spróbuj ponownie.')->withInput();
        }
    }
}
