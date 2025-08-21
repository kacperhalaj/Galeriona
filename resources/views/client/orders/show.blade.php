@extends('client.panel')

@section('content')
    <h2>Zamówienie #{{ $order->id }}</h2>
    <p>Status: <strong>{{ $order->status_label }}</strong></p>
    <p>Data: {{ $order->created_at->format('d.m.Y H:i') }}</p>
    <table class="table">
        <thead>
            <tr>
                <th>Dzieło</th>
                <th>Cena</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderItems as $item)
                <tr>
                    <td>{{ $item->artwork->title }}</td>
                    <td>{{ number_format($item->price, 2, ',', ' ') }} zł</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <strong>Łącznie: {{ number_format($order->total_price, 2, ',', ' ') }} zł</strong>
@endsection
