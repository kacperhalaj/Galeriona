@extends('seller.panel')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-users me-2"></i>Obserwujący Twój profil</h2>
    </div>

    <div class="table-responsive shadow-sm rounded bg-white">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nazwa użytkownika</th>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @forelse($followers as $follower)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $follower->username }}</td>
                        <td>{{ $follower->first_name ?? '-' }}</td>
                        <td>{{ $follower->last_name ?? '-' }}</td>
                        <td>{{ $follower->email }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Brak obserwujących.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection