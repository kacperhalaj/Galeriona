@extends('admin.panel')

@section('content')
    <div class="container mt-4">
        <h2>Lista Użytkowników</h2>
        <div class="mb-3">
            <form class="row g-2" method="GET" action="{{ route('admin.manage.users.index') }}">
                <div class="col-auto">
                    <input type="text" name="keyword" class="form-control" placeholder="Szukaj użytkownika..."
                        value="{{ $keyword ?? '' }}">
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i> Filtruj</button>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.manage.users.index') }}" class="btn btn-outline-secondary">Wyczyść</a>
                </div>
            </form>
        </div>
        <a href="{{ route('admin.manage.users.create') }}" class="btn btn-primary mb-2">Dodaj klienta</a>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nazwa użytkownika</th>
                    <th>Email</th>
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
                        <td>{{ $user->created_at }}</td>
                        <td>
                            <a href="{{ route('admin.manage.users.show', $user) }}" class="btn btn-info btn-sm">Podgląd</a>
                            <a href="{{ route('admin.manage.users.edit', $user) }}" class="btn btn-warning btn-sm">Edytuj</a>
                            <form action="{{ route('admin.manage.users.destroy', $user) }}" method="POST"
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
        @if ($users->hasPages())
            <nav>
                <ul class="pagination justify-content-center">
                    <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $users->previousPageUrl() ?? '#' }}" rel="prev"
                            aria-label="Poprzednia">
                            &laquo; Poprzednia
                        </a>
                    </li>
                    @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                        <li class="page-item {{ $page == $users->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach
                    <li class="page-item {{ !$users->hasMorePages() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $users->nextPageUrl() ?? '#' }}" rel="next"
                            aria-label="Następna">
                            Następna &raquo;
                        </a>
                    </li>
                </ul>
                <div class="text-center mt-2 small text-muted">
                    Wyświetlane od {{ $users->firstItem() }} do {{ $users->lastItem() }} z {{ $users->total() }} wyników
                </div>
            </nav>
        @endif
        <footer class="mt-5 py-4 bg-transparent"></footer>
    </div>
@endsection
