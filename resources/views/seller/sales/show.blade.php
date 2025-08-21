@extends('seller.panel')

@section('content')
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Szczegóły sprzedaży #{{ $sale->id }}</h4>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-md-3">Dzieło</dt>
                <dd class="col-md-9">{{ $sale->artwork->title ?? '-' }}</dd>

                <dt class="col-md-3">Kupujący</dt>
                <dd class="col-md-9">{{ $sale->user->username ?? '-' }}</dd>

                <dt class="col-md-3">Cena</dt>
                <dd class="col-md-9">{{ number_format($sale->price, 2, ',', ' ') }} zł</dd>

                <dt class="col-md-3">Data sprzedaży</dt>
                <dd class="col-md-9">{{ $sale->sold_at ? \Carbon\Carbon::parse($sale->sold_at)->format('d.m.Y H:i') : '-' }}
                </dd>
            </dl>
            <a href="{{ route('seller.sales.index') }}" class="btn btn-outline-secondary mt-3">
                <i class="fas fa-arrow-left"></i> Powrót do historii sprzedaży
            </a>
        </div>
    </div>
@endsection
