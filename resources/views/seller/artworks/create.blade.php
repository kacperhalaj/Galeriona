@extends('seller.panel')

@section('content')
<div class="container">
    <h1>Dodaj nowe dzieło</h1>
    <form action="{{ route('seller.artworks.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">Tytuł</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
        </div>
        <div class="mb-3">
            <label for="artist" class="form-label">Artysta</label>
            <input type="text" name="artist" class="form-control" value="{{ old('artist') }}">
        </div>
        <div class="mb-3" id="price-field">
            <label for="price" class="form-label">Cena (zl)</label>
            <input type="number" step="0.01" name="price" class="form-control" id="price" value="{{ old('price', $artwork->price ?? '') }}">
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_priceless" id="is_priceless" value="1" {{ old('is_priceless', $artwork->is_priceless ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_priceless">Bezcenne</label>
        </div>
        <div class="mb-3">
            <label for="width" class="form-label">Szerokość (cm)</label>
            <input type="number" step="0.01" name="width" class="form-control" value="{{ old('width') }}">
        </div>
        <div class="mb-3">
            <label for="height" class="form-label">Wysokość (cm)</label>
            <input type="number" step="0.01" name="height" class="form-control" value="{{ old('height') }}">
        </div>
        <div class="mb-3">
            <label for="depth" class="form-label">Głębokość (cm)</label>
            <input type="number" step="0.01" name="depth" class="form-control" value="{{ old('depth') }}">
        </div>
        <div class="mb-3">
            <label for="category_id" class="form-label">Kategoria</label>
            <select name="category_id" id="category_id" class="form-control">
                <option value="">Wybierz kategorię</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Opis</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Ścieżka do obrazu</label>
            <input type="file" name="image" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Zapisz</button>
        <a href="{{ route('seller.artworks.index') }}" class="btn btn-secondary">Anuluj</a>
    </form>
</div>
<script>
document.getElementById('is_priceless').addEventListener('change', function() {
    document.getElementById('price-field').style.display = this.checked ? 'none' : 'block';
});
document.getElementById('price-field').style.display = document.getElementById('is_priceless').checked ? 'none' : 'block';
</script>
@endsection
