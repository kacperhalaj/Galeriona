@extends('seller.panel')

@section('content')
    <div class="container">
        <h4 class="mb-4 text-primary">
            <i class="fas fa-hand-holding-usd me-2"></i>
            Wypłać Środki
        </h4>

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <p class="fs-5">Dostępne saldo do wypłaty: <strong class="text-success">PLN {{ number_format($current_balance, 2, ',', ' ') }}</strong></p>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('seller.finances.withdraw.process') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="amount" class="form-label">Kwota do wypłaty (PLN)</label>
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" max="{{ $current_balance }}" value="{{ old('amount') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="bank_account_number" class="form-label">Numer Konta Bankowego (IBAN)</label>
                        <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number') }}" placeholder="np. PL12345678901234567890123456" required>
                        <div class="form-text">Wprowadź pełny numer konta w formacie IBAN.</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-paper-plane me-2"></i>
                        Zleć Wypłatę
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('seller.finances.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Powrót do finansów
            </a>
        </div>
    </div>
@endsection
