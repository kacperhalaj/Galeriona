@extends('client.panel')

@section('panel_content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="fas fa-wallet me-2"></i>Mój Portfel</h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        {{-- Podsumowanie portfela --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="fs-5 mb-1">Twoje aktualne saldo:</p>
                        <h3 class="text-success fw-bold mb-0">PLN {{ number_format($balance, 2, ',', ' ') }}</h3>
                    </div>
                    <a href="{{ route('client.wallet.topup.form') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>
                        Doładuj Saldo
                    </a>
                </div>
            </div>
        </div>

        <h5 class="mb-3 text-primary"><i class="fas fa-history me-2"></i>Ostatnie Zmiany Salda</h5>
        @if ($balanceHistories->isEmpty())
            <div class="alert alert-info" role="alert">
                Brak historii zmian salda.
            </div>
        @else
            <ul class="list-group shadow-sm">
                @foreach ($balanceHistories as $history)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            {{ $history->created_at->format('d.m.Y H:i') }} - {{ $history->description ?? ucfirst(str_replace('_', ' ', $history->type)) }}
                        </span>
                        <span class="fw-bold {{ $history->amount > 0 ? 'text-success' : 'text-danger' }}">
                            {{ ($history->amount > 0 ? '+' : '') . number_format($history->amount, 2, ',', ' ') }} PLN
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif

    </div>
@endsection
