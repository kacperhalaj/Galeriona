@extends('admin.panel')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i>Szczegóły sprzedaży</h4>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">ID</dt>
                    <dd class="col-sm-9">{{ $sale->id }}</dd>

                    <dt class="col-sm-3">Dzieło sztuki</dt>
                    <dd class="col-sm-9">{{ $sale->artwork->title ?? '—' }}</dd>

                    <dt class="col-sm-3">Kupujący</dt>
                    <dd class="col-sm-9">{{ $sale->user->username ?? '—' }}</dd>

                    <dt class="col-sm-3">Cena</dt>
                    <dd class="col-sm-9">{{ number_format($sale->price, 2, ',', ' ') }} zł</dd>

                    <dt class="col-sm-3">Data sprzedaży</dt>
                    <dd class="col-sm-9">{{ $sale->sold_at }}</dd>

                    <dt class="col-sm-3">Utworzono</dt>
                    <dd class="col-sm-9">{{ $sale->created_at }}</dd>
                </dl>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.sales.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Powrót
                </a>
                <a href="{{ route('admin.sales.edit', $sale) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Edytuj
                </a>
            </div>
        </div>
    </div>
@endsection
