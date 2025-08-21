@extends('admin.panel')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edytuj sprzedaż</h4>
            </div>
            <form method="POST" action="{{ route('admin.sales.update', $sale) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label for="artwork_id" class="form-label">Dzieło sztuki</label>
                        <select name="artwork_id" id="artwork_id" class="form-control" required onchange="setPrice()">
                            @foreach ($artworks as $artwork)
                                <option value="{{ $artwork->id }}" data-price="{{ $artwork->price }}"
                                    @if ($sale->artwork_id == $artwork->id) selected @endif>
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
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @if ($sale->user_id == $user->id) selected @endif>
                                    {{ $user->username }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Cena</label>
                        <input type="number" step="0.01" name="price" id="price" class="form-control"
                            value="{{ $sale->price }}" readonly>
                        @error('price')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="sold_at" class="form-label">Data sprzedaży</label>
                        <input type="datetime-local" name="sold_at" id="sold_at" class="form-control"
                            value="{{ \Carbon\Carbon::parse($sale->sold_at)->format('Y-m-d\TH:i') }}" readonly>
                        @error('sold_at')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-success"><i class="fas fa-save me-1"></i> Zapisz zmiany</button>
                    <a href="{{ route('admin.sales.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Anuluj
                    </a>
                </div>
            </form>
        </div>
    </div>
    <script>
        function setPrice() {
            let select = document.getElementById('artwork_id');
            let price = select.options[select.selectedIndex].getAttribute('data-price');
            document.getElementById('price').value = price ? price : '';
        }
        document.addEventListener('DOMContentLoaded', setPrice);
    </script>
@endsection
