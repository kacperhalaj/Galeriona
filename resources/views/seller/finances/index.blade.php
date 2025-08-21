@extends('seller.panel')

@section('content')
    <div class="container">
        <h4 class="mb-4 text-primary">
            <i class="fas fa-dollar-sign me-2"></i>
            Moje Finanse
        </h4>

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

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="fs-5 mb-1">Twoje aktualne saldo:</p>
                        <h3 class="text-success fw-bold mb-0">PLN {{ number_format($balance, 2, ',', ' ') }}</h3>
                    </div>
                    <a href="{{ route('seller.finances.withdraw.form') }}" class="btn btn-info text-white">
                        <i class="fas fa-hand-holding-usd me-2"></i>
                        Wypłać Środki
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form action="{{ route('seller.finances.statement') }}" method="POST">
                    @csrf
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <label for="month" class="col-form-label">Wybierz miesiąc:</label>
                        </div>
                        <div class="col-auto">
                            <input type="month" id="month" name="month" class="form-control" required>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-pdf me-2"></i>
                                Generuj PDF
                            </button>
                        </div>
                    </div>
                </form>
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
