@extends('client.panel')

@section('content')
    <div class="container">
        <h4 class="mb-4 text-primary">
            <i class="fas fa-wallet me-2"></i>
            Doładuj Portfel
        </h4>

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('client.wallet.topup.process') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="amount" class="form-label">Kwota doładowania (PLN)</label>
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Metoda płatności</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="" disabled {{ old('payment_method') ? '' : 'selected' }}>Wybierz metodę płatności...</option>
                            <option value="blik" {{ old('payment_method') == 'blik' ? 'selected' : '' }}>BLIK</option>
                            <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Karta płatnicza</option>
                            <option value="paysafecard" {{ old('payment_method') == 'paysafecard' ? 'selected' : '' }}>Paysafecard</option>
                            <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Przelew tradycyjny</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-plus-circle me-2"></i>
                        Doładuj
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('client.wallet.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Powrót do portfela
            </a>
        </div>
    </div>
@endsection
