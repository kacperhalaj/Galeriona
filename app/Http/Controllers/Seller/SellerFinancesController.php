<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BalanceHistory;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\DB; 

class SellerFinancesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $balance = $user->balance;
        $balanceHistories = $user->balanceHistories()->orderBy('created_at', 'desc')->take(10)->get();
        return view('seller.finances.index', compact('user', 'balance', 'balanceHistories'));
    }

    /**
     * Wyświetla formularz wypłaty.
     */
    public function showWithdrawForm()
    {
        $user = Auth::user();
        return view('seller.finances.withdraw', ['current_balance' => $user->balance]);
    }

    /**
     * Przetwarza żądanie wypłaty.
     */
    public function processWithdraw(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric', 'min:0.01', function ($attribute, $value, $fail) use ($user) {
                if (bccomp((string)$user->balance, (string)$value, 2) === -1) {
                    $fail('Nie masz wystarczających środków do wypłaty tej kwoty.');
                }
            }],
            'bank_account_number' => 'required|string|min:10|max:34',
        ], [
            'amount.max' => 'Nie masz wystarczających środków do wypłaty tej kwoty.'
        ]);

        if ($validator->fails()) {
            return redirect()->route('seller.finances.withdraw.form')
                ->withErrors($validator)
                ->withInput();
        }

        $amount = (float) $request->input('amount');
        $bankAccountNumber = $request->input('bank_account_number');

        DB::beginTransaction();
        try {
            $user->balance -= $amount;
            $user->save();

            BalanceHistory::create([
                'user_id' => $user->id,
                'amount' => -$amount, // Kwota ujemna dla wypłaty
                'type' => 'withdrawal',
                'description' => 'Wypłata na konto bankowe: ...' . substr($bankAccountNumber, -4), // Maskowanie numeru konta
            ]);

            DB::commit();

            return redirect()->route('seller.finances.index')->with('success', 'Zlecenie wypłaty na kwotę ' . number_format($amount, 2, ',', ' ') . ' PLN zostało przyjęte.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Błąd podczas przetwarzania wypłaty: ' . $e->getMessage());
            return redirect()->route('seller.finances.withdraw.form')->with('error', 'Wystąpił błąd podczas przetwarzania wypłaty. Spróbuj ponownie.')->withInput();
        }
    }
}
