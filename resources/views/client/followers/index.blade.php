@extends('client.panel')

@section('panel_content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-heart me-2"></i>Zaobserwowani artyści</h2>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive shadow-sm rounded bg-white">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nazwa użytkownika</th>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                @forelse($artists as $artist)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $artist->username }}</td>
                        <td>{{ $artist->first_name ?? '-' }}</td>
                        <td>{{ $artist->last_name ?? '-' }}</td>
                        <td>
                            <form action="{{ route('seller.unfollow', $artist->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-user-minus me-1"></i> Przestań obserwować
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Brak zaobserwowanych artystów.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection