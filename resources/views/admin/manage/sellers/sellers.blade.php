@extends('admin.panel')

@section('content')
    <div class="container mt-4">
        <h2>Lista Sprzedawców</h2>
        <div class="mb-3">
            <form class="row g-2" method="GET" action="{{ route('admin.manage.sellers.index') }}">
                <div class="col-auto">
                    <input type="text" name="keyword" class="form-control" placeholder="Szukaj sprzedawcy..."
                        value="{{ $keyword ?? '' }}">
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i> Filtruj</button>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.manage.sellers.index') }}" class="btn btn-outline-secondary">Wyczyść</a>
                </div>
            </form>
        </div>
        <a href="{{ route('admin.manage.sellers.create') }}" class="btn btn-primary mb-2">Dodaj sprzedawcę</a>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nazwa użytkownika</th>
                    <th>Email</th>
                    <th>Miasto</th>
                    <th>Data rejestracji</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ optional($user->addresses->first())->city ?? '-' }}</td>
                        <td>{{ $user->created_at }}</td>
                        <td>
                            <a href="{{ route('admin.manage.sellers.show', $user) }}" class="btn btn-info btn-sm">Podgląd</a>
                            <a href="{{ route('admin.manage.sellers.edit', $user) }}" class="btn btn-warning btn-sm">Edytuj</a>
                            <form action="{{ route('admin.manage.sellers.destroy', $user) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Na pewno usunąć?');" class="btn btn-danger btn-sm">Usuń</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($users->hasPages())
            <nav>
                <ul class="pagination justify-content-center">
                    <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $users->previousPageUrl() ?? '#' }}" rel="prev" aria-label="Poprzednia">
                            &laquo; Poprzednia
                        </a>
                    </li>
                    @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                        <li class="page-item {{ $page == $users->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach
                    <li class="page-item {{ !$users->hasMorePages() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $users->nextPageUrl() ?? '#' }}" rel="next" aria-label="Następna">
                            Następna &raquo;
                        </a>
                    </li>
                </ul>
                <div class="text-center mt-2 small text-muted">
                    Wyświetlane od {{ $users->firstItem() }} do {{ $users->lastItem() }} z {{ $users->total() }} wyników
                </div>
            </nav>
        @endif
    </div>
@endsection
