@extends('admin.panel')

@section('content')
    <div class="container">
        <h1>Edytuj adres</h1>

        {{-- Wyświetlanie błędów walidacji --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.addresses.update', $address->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Ukryte pole dla identyfikatora użytkownika --}}
            <input type="hidden" name="user_id" value="{{ $address->user_id }}">

            <div class="form-group">
                <label for="user_id_display">Użytkownik:</label>
                <select disabled id="user_id_display" class="form-control">
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ $address->user_id == $user->id ? 'selected' : '' }}>
                            {{ $user->username }} ({{ $user->first_name }} {{ $user->last_name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="city">Miasto:</label>
                <input type="text" name="city" id="city" class="form-control" value="{{ old('city', $address->city) }}" required>
            </div>

            <div class="form-group">
                <label for="street">Ulica:</label>
                <input type="text" name="street" id="street" class="form-control" value="{{ old('street', $address->street) }}" required>
            </div>

            <div class="form-group">
                <label for="postal_code">Kod pocztowy:</label>
                <input type="text" name="postal_code" id="postal_code" class="form-control" value="{{ old('postal_code', $address->postal_code) }}" required>
            </div>

            <div class="form-group">
                <label for="house_number">Numer domu:</label>
                <input type="text" name="house_number" id="house_number" class="form-control" value="{{ old('house_number', $address->house_number) }}" required>
            </div>

            <div class="form-group">
                <label for="apartment_number">Numer mieszkania:</label>
                <input type="text" name="apartment_number" id="apartment_number" class="form-control" value="{{ old('apartment_number', $address->apartment_number) }}">
            </div>

            <button type="submit" class="btn btn-primary">Zaktualizuj adres</button>
            <a href="{{ route('admin.addresses.index', ['user_id' => $address->user_id]) }}" class="btn btn-secondary">Anuluj</a>
        </form>
    </div>
@endsection
