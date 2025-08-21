@extends('client.panel')

@section('content')
    <h2>Moje zamówienia</h2>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Data</th>
                <th>Liczba dzieł</th>
                <th>Kwota</th>
                <th>Status</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                    <td>{{ $order->orderItems->count() }}</td>
                    <td>{{ number_format($order->total_price, 2, ',', ' ') }} zł</td>
                    <td>{{ $order->status_label }}</td>
                    <td>
                        <a href="{{ route('client.orders.show', $order) }}" class="btn btn-info btn-sm">Szczegóły</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Brak zamówień</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{ $orders->links() }}
@endsection
