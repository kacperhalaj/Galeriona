<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zestawienie Sprzedaży - {{ \Carbon\Carbon::parse($month)->locale('pl')->translatedFormat('F Y') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #7f8c8d;
        }
        .seller-info, .summary-info {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .seller-info p, .summary-info p {
            margin: 5px 0;
            font-size: 14px;
        }
        .sales-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .sales-table th, .sales-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        .sales-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total-row td {
            font-weight: bold;
            background-color: #e8f6f3;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Zestawienie Sprzedaży</h1>
        <p>Miesiąc: {{ \Carbon\Carbon::parse($month)->locale('pl')->translatedFormat('F Y') }}</p>
        <p>Sprzedawca: {{ $user->name }} ({{ $user->email }})</p>
        <p>Data wygenerowania: {{ $generationDate }}</p>
    </div>

    <div class="summary-info">
        <p><strong>Całkowity przychód w tym miesiącu:</strong> {{ number_format($totalSales, 2, ',', ' ') }} PLN</p>
    </div>

    <h5>Szczegółowa lista sprzedaży:</h5>
    @if ($sales->isEmpty())
        <p>Brak sprzedaży w wybranym miesiącu.</p>
    @else
        <table class="sales-table">
            <thead>
                <tr>
                    <th>ID Zamówienia</th>
                    <th>Data Sprzedaży</th>
                    <th>Produkt</th>
                    <th>Suma</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales as $sale_item)
                    <tr>
                        <td>{{ $sale_item->id }}</td>
                        <td>{{ $sale_item->sold_at ? \Carbon\Carbon::parse($sale_item->sold_at)->format('d.m.Y H:i') : 'N/A' }}</td>
                        <td>{{ $sale_item->artwork ? $sale_item->artwork->title : 'Produkt usunięty' }}</td>
                        <td>{{ number_format($sale_item->price, 2, ',') }} PLN</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" style="text-align:right;"><strong>Łącznie:</strong></td>
                    <td><strong>{{ number_format($totalSales, 2, ',', ' ') }} PLN</strong></td>
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="footer">
        Wygenerowano automatycznie przez system {{ config('app.name') }}
    </div>
</body>
</html>
