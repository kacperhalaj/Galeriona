@extends('admin.panel')

@section('content')
    <div class="container mt-4">
        <h2>Lista Sprzedaży</h2>
        <div class="mb-3">
            <form class="row g-2" method="GET" action="{{ route('admin.sales.index') }}">
                <div class="col-auto">
                    <input type="text" name="keyword" class="form-control" placeholder="Szukaj dzieła lub artysty..."
                        value="{{ $keyword ?? '' }}">
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i> Filtruj</button>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.sales.index') }}" class="btn btn-outline-secondary">Wyczyść</a>
                </div>
            </form>
        </div>
        <a href="{{ route('admin.sales.create') }}" class="btn btn-primary mb-2">Dodaj sprzedaż</a>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Dzieło sztuki</th>
                    <th>Artysta</th>
                    <th>Kupujący</th>
                    <th>Cena</th>
                    <th>Data sprzedaży</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales as $sale)
                    <tr>
                        <td>{{ $sale->id }}</td>
                        <td>{{ $sale->artwork->title ?? '—' }}</td>
                        <td>{{ $sale->artwork->artist ?? '—' }}</td>
                        <td>{{ $sale->user->username ?? '—' }}</td>
                        <td>{{ number_format($sale->price, 2, ',', ' ') }}</td>
                        <td>{{ $sale->sold_at }}</td>
                        <td>
                            <a href="{{ route('admin.sales.show', $sale) }}" class="btn btn-info btn-sm">Podgląd</a>
                            <a href="{{ route('admin.sales.edit', $sale) }}" class="btn btn-warning btn-sm">Edytuj</a>
                            <form action="{{ route('admin.sales.destroy', $sale) }}" method="POST"
                                style="display:inline;">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Na pewno usunąć?');"
                                    class="btn btn-danger btn-sm">Usuń</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

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
                        <a class="page-link" href="{{ $sales->nextPageUrl() ?? '#' }}" rel="next"
                            aria-label="Następna">
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
    </div>
@endsection
