@extends('admin.panel')

@section('content')
    <div class="container mt-4">
        <h2>Dodaj sprzedaż</h2>
        <form method="POST" action="{{ route('admin.sales.store') }}">
            @csrf
            <div class="mb-3">
                <label for="artwork_id" class="form-label">Dzieło sztuki</label>
                <select name="artwork_id" id="artwork_id" class="form-control" required onchange="setPrice()">
                    <option value="">Wybierz</option>
                    @foreach ($artworks as $artwork)
                        <option value="{{ $artwork->id }}" data-price="{{ $artwork->price }}">
                            {{ $artwork->title }} / {{ $artwork->artist }} / dodano:
                            {{ $artwork->created_at ? $artwork->created_at->format('Y-m-d H:i') : '-' }}
                        </option>
                    @endforeach
                </select>
                @error('artwork_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="user_id" class="form-label">Kupujący</label>
                <select name="user_id" id="user_id" class="form-control" required>
                    <option value="">Wybierz</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->username }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('user_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Cena</label>
                <input type="number" step="0.01" name="price" id="price" class="form-control" readonly>
                @error('price')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="sold_at" class="form-label">Data sprzedaży</label>
                <input type="datetime-local" name="sold_at" id="sold_at" class="form-control"
                    value="{{ old('sold_at', $defaultSoldAt) }}" readonly>
                @error('sold_at')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <button class="btn btn-success">Zapisz</button>
            <a href="{{ route('admin.sales.index') }}" class="btn btn-secondary">Anuluj</a>
        </form>
    </div>
    <script>
        function setPrice() {
            let select = document.getElementById('artwork_id');
            let price = select.options[select.selectedIndex].getAttribute('data-price');
            document.getElementById('price').value = price ? price : '';
        }
    </script>
@endsection
