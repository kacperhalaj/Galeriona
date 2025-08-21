@extends('seller.panel')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Historia sprzedaży</h2>
    </div>

    {{-- Pole do filtrowania --}}
    <form method="GET" action="{{ route('seller.sales.index') }}" class="mb-4">
        <div class="input-group" style="max-width: 400px;">
            <input type="text" name="keyword" class="form-control" placeholder="Szukaj dzieło lub kupującego"
                value="{{ request('keyword') }}">
            <button class="btn btn-primary" type="submit">Szukaj</button>
            <a href="{{ route('seller.sales.index') }}" class="btn btn-outline-secondary">Wyczyść</a>
        </div>
    </form>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive shadow-sm rounded bg-white">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Dzieło</th>
                    <th>Kupujący</th>
                    <th>Cena</th>
                    <th>Data sprzedaży</th>
                    <th class="text-end">Akcje</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr>
                        <td>{{ $sale->id }}</td>
                        <td>{{ $sale->artwork->title ?? '-' }}</td>
                        <td>{{ $sale->buyer->username ?? '-' }}</td>
                        <td>{{ number_format($sale->price, 2, ',', ' ') }} zł</td>
                        <td>{{ $sale->sold_at ? \Carbon\Carbon::parse($sale->sold_at)->format('d.m.Y H:i') : '-' }}</td>
                        <td class="text-end">
                            <a href="{{ route('seller.sales.show', $sale->id) }}" class="btn btn-sm btn-outline-info me-1">
                                <i class="fas fa-eye"></i> Szczegóły
                            </a>
                            <form action="{{ route('seller.sales.destroy', $sale->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Na pewno usunąć tę sprzedaż?')"
                                    class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Brak historii sprzedaży.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINACJA --}}
    @if ($sales->hasPages())
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item {{ $sales->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $sales->previousPageUrl() ?? '#' }}" rel="prev"
                        aria-label="Poprzednia">
                        &laquo; Poprzednia
                    </a>
                </li>
                @foreach ($sales->getUrlRange(1, $sales->lastPage()) as $page => $url)
                    <li class="page-item {{ $page == $sales->currentPage() ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endforeach
                <li class="page-item {{ !$sales->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $sales->nextPageUrl() ?? '#' }}" rel="next" aria-label="Następna">
                        Następna &raquo;
                    </a>
                </li>
            </ul>
            <div class="text-center mt-2 small text-muted">
                Wyświetlane od {{ $sales->firstItem() }} do {{ $sales->lastItem() }} z {{ $sales->total() }} wyników
            </div>
        </nav>
    @endif
    <footer class="mt-5 py-4 bg-transparent"></footer>
@endsection
