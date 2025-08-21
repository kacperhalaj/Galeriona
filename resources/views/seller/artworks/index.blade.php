@extends('seller.panel')

@section('content')
<div class="container">
    <h1>Twoje dzieła</h1>
    <a href="{{ route('seller.artworks.create') }}" class="btn btn-success mb-3">Dodaj nowe dzieło</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th>Tytuł</th>
                <th>Artysta</th>
                <th>Cena</th>
                <th>Kategoria</th>
                <th>Wymiary</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($artworks as $artwork)
                <tr>
                    <td>{{ $artwork->title }}</td>
                    <td>{{ $artwork->artist }}</td>
                    <td>
                        @if($artwork->is_priceless)
                            <span class="text-info">Bezcenne</span>
                        @elseif(!is_null($artwork->price) && $artwork->price !== '')
                            {{ $artwork->price }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $artwork->price }}</td>
                    <td>{{ $artwork->category->name ?? 'Brak' }}</td>
                    <td>
                        {{ $artwork->width ?? '-' }} x {{ $artwork->height ?? '-' }} x {{ $artwork->depth ?? '-' }}
                    </td>
                    <td>
                        <a href="{{ route('seller.artworks.show', $artwork) }}" class="btn btn-info btn-sm">Podgląd</a>
                        <a href="{{ route('seller.artworks.edit', $artwork) }}" class="btn btn-primary btn-sm">Edytuj</a>
                        <form action="{{ route('seller.artworks.destroy', $artwork) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Na pewno usunąć?')">Usuń</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
