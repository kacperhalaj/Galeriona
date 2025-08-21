@extends('client.panel')

@section('panel_content')
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Szczegóły zakupu #{{ $purchase->id }}</h4>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-md-3">Dzieło</dt>
                <dd class="col-md-9">{{ $purchase->artwork->title ?? '-' }}</dd>

                <dt class="col-md-3">Artysta</dt>
                <dd class="col-md-9">{{ $purchase->artwork->artist ?? '-' }}</dd>

                <dt class="col-md-3">Sprzedawca</dt>
                <dd class="col-md-9">{{ $purchase->artwork->seller->username ?? '-' }}</dd>

                <dt class="col-md-3">Cena</dt>
                <dd class="col-md-9">{{ number_format($purchase->price, 2, ',', ' ') }} zł</dd>

                <dt class="col-md-3">Data zakupu</dt>
                <dd class="col-md-9">
                    {{ $purchase->sold_at ? \Carbon\Carbon::parse($purchase->sold_at)->format('d.m.Y H:i') : '-' }}</dd>
            </dl>
            <a href="{{ route('client.purchases.index') }}" class="btn btn-outline-secondary mt-3">
                <i class="fas fa-arrow-left"></i> Powrót do zakupów
            </a>
        </div>
    </div>
@endsection
