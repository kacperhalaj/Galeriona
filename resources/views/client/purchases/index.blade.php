@extends('client.panel')

@section('panel_content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-shopping-basket me-2"></i>Twoje zakupy</h2>
    </div>

    <form method="GET" action="{{ route('client.purchases.index') }}" class="mb-4">
        <div class="input-group" style="max-width: 600px;">
            <input type="text" name="keyword" class="form-control" placeholder="Szukaj dzieło, artystę lub sprzedającego"
                value="{{ request('keyword') }}">
            <button class="btn btn-primary" type="submit">Szukaj</button>
            <a href="{{ route('client.purchases.index') }}" class="btn btn-outline-secondary">Wyczyść</a>
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
                    <th>Artysta</th>
                    <th>Sprzedawca</th>
                    <th>Cena</th>
                    <th>Data zakupu</th>
                    <th class="text-end">Akcje</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->id }}</td>
                        <td>{{ $purchase->artwork->title ?? '-' }}</td>
                        <td>{{ $purchase->artwork->artist ?? '-' }}</td>
                        <td>{{ $purchase->artwork->seller->username ?? '-' }}</td>
                        <td>{{ number_format($purchase->price, 2, ',', ' ') }} zł</td>
                        <td>{{ $purchase->sold_at ? \Carbon\Carbon::parse($purchase->sold_at)->format('d.m.Y H:i') : '-' }}
                        </td>
                        <td class="text-end">
                            <a href="{{ route('client.purchases.show', $purchase->id) }}"
                                class="btn btn-sm btn-outline-info">
                                <i class="fas fa-eye"></i> Szczegóły
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Brak historii zakupów.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINACJA --}}
    @if ($purchases->hasPages())
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item {{ $purchases->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $purchases->previousPageUrl() ?? '#' }}" rel="prev"
                        aria-label="Poprzednia">
                        &laquo; Poprzednia
                    </a>
                </li>
                @foreach ($purchases->getUrlRange(1, $purchases->lastPage()) as $page => $url)
                    <li class="page-item {{ $page == $purchases->currentPage() ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endforeach
                <li class="page-item {{ !$purchases->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $purchases->nextPageUrl() ?? '#' }}" rel="next"
                        aria-label="Następna">
                        Następna &raquo;
                    </a>
                </li>
            </ul>
            <div class="text-center mt-2 small text-muted">
                Wyświetlane od {{ $purchases->firstItem() }} do {{ $purchases->lastItem() }} z {{ $purchases->total() }}
                wyników
            </div>
        </nav>
    @endif

    <footer class="mt-5 py-4 bg-transparent"></footer>
@endsection
