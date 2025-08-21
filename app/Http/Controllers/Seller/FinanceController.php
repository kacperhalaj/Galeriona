<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BalanceHistory;
use App\Models\Sale;
use Mpdf\Mpdf;
use Carbon\Carbon;

class FinanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $balance = $user->balance;
        $balanceHistories = BalanceHistory::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('seller.finances.index', compact('balance', 'balanceHistories'));
    }

    public function generateStatement(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $user = Auth::user();
        $selectedMonth = Carbon::createFromFormat('Y-m', $request->month);
        $startDate = $selectedMonth->copy()->startOfMonth();
        $endDate = $selectedMonth->copy()->endOfMonth();


        $sales = Sale::whereHas('artwork', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->whereBetween('sold_at', [$startDate, $endDate])
        ->with('artwork')
        ->get();


        $totalSales = $sales->sum('price');

        // przygotowanie danych do PDF
        $data = [
            'user' => $user,
            'sales' => $sales,
            'totalSales' => $totalSales,
            'month' => $selectedMonth->format('F Y'),
            'generationDate' => Carbon::now()->format('d.m.Y H:i'),
        ];

        // generowanie PDF
        $pdf = new \Mpdf\Mpdf();

        $html = view('seller.finances.statement_pdf', $data)->render();
        $pdf->WriteHTML($html);


        $safeMonthString = str_replace(' ', '_', $selectedMonth->format('Y_m'));
        return $pdf->Output('zestawienie_sprzedazy_' . $safeMonthString . '.pdf', 'D');
    }


    public function withdrawForm()
    {
        return view('seller.finances.withdraw');
    }

    public function processWithdrawal(Request $request)
    {
        return redirect()->route('seller.finances.index')->with('success', 'Wypłata przetworzona pomyślnie.');
    }
}
