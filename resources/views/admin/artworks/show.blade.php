@extends('admin.panel')

@section('content')
<div class="container">
    <h1>{{ $artwork->title }}</h1>
    <p><strong>Artysta:</strong> {{ $artwork->artist }}</p>
    <p><strong>Właściciel:</strong> {{ $artwork->user->name ?? '-' }}</p>
    <p><strong>Cena:</strong> {{ $artwork->price }}</p>
    <p><strong>Wymiary:</strong> {{ $artwork->width ?? '-' }} x {{ $artwork->height ?? '-' }} x {{ $artwork->depth ?? '-' }} cm</p>
    <p><strong>Opis:</strong> {{ $artwork->description }}</p>
    @if($artwork->image_path)
        <img src="{{ asset($artwork->image_path) }}" alt="Obraz" style="max-width:300px;">
    @endif
    <div class="mt-3">
        <a href="{{ route('admin.artworks.edit', $artwork) }}" class="btn btn-primary">Edytuj</a>
        <form action="{{ route('admin.artworks.destroy', $artwork) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger" onclick="return confirm('Na pewno usunąć?')">Usuń</button>
        </form>
        <a href="{{ route('admin.artworks.index') }}" class="btn btn-secondary">Powrót</a>
    </div>
</div>
@endsection
