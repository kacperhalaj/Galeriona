@extends('admin.panel')

@section('content')
    <div class="container">
        <h1>Dodaj nowy adres</h1>

        {{-- Wyświetl błędy walidacji --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.addresses.store') }}" method="POST">
            @csrf
            {{-- Ukryte pole z ID użytkownika --}}
            <input type="hidden" name="user_id" value="{{ $selectedUserId }}">

            <div class="form-group">
                <label for="user_id_display">Użytkownik:</label>
                <select disabled id="user_id_display" class="form-control">
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ $selectedUserId == $user->id ? 'selected' : '' }}>
                            {{ $user->username }} ({{ $user->first_name }} {{ $user->last_name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="city">Miasto:</label>
                <input type="text" name="city" id="city" class="form-control" value="{{ old('city') }}" required>
            </div>

            <div class="form-group">
                <label for="postal_code">Kod pocztowy:</label>
                <input type="text" name="postal_code" id="postal_code" class="form-control" value="{{ old('postal_code') }}" required>
            </div>

            <div class="form-group">
                <label for="street">Ulica:</label>
                <input type="text" name="street" id="street" class="form-control" value="{{ old('street') }}" required>
            </div>

            <div class="form-group">
                <label for="house_number">Numer domu:</label>
                <input type="text" name="house_number" id="house_number" class="form-control" value="{{ old('house_number') }}" required>
            </div>

            <div class="form-group">
                <label for="apartment_number">Numer mieszkania:</label>
                <input type="text" name="apartment_number" id="apartment_number" class="form-control" value="{{ old('apartment_number') }}">
            </div>

            <button type="submit" class="btn btn-primary">Dodaj adres</button>
            <a href="{{ route('admin.addresses.index', ['user_id' => $selectedUserId]) }}" class="btn btn-secondary">Anuluj</a>
        </form>
    </div>

<script>
    
        // Formatowanie pola kodu pocztowego
        document.getElementById('postal_code').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '-' + value.substring(2, 5);
            }
            this.value = value;
        });
</script>
@endsection
