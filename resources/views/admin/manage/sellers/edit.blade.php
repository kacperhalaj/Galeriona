@extends('admin.panel')

@section('content')
    <div class="container mt-4">
        <h2>Edytuj sprzedawcę</h2>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.manage.sellers.update', $user) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="username" class="form-label">Nazwa użytkownika</label>
                <input type="text" name="username" id="username" class="form-control"
                       value="{{ old('username', $user->username) }}" required>
                @error('username')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="first_name" class="form-label">Imię</label>
                <input type="text" name="first_name" id="first_name" class="form-control"
                       value="{{ old('first_name', $user->first_name) }}" required>
                @error('first_name')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Nazwisko</label>
                <input type="text" name="last_name" id="last_name" class="form-control"
                       value="{{ old('last_name', $user->last_name) }}" required>
                @error('last_name')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" name="email" id="email" class="form-control"
                       value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Hasło (jeśli chcesz zmienić)</label>
                <input type="password" name="password" id="password" class="form-control">
                @error('password')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Powtórz hasło</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
            </div>
            @php $address = $user->addresses->first(); @endphp
            <div class="mb-3">
                <label for="city" class="form-label">Miasto</label>
                <input type="text" name="city" id="city" class="form-control" value="{{ old('city', $address->city ?? '') }}" required>
                @error('city')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="postal_code" class="form-label">Kod pocztowy</label>
                <input type="text" name="postal_code" id="postal_code" class="form-control" value="{{ old('postal_code', $address->postal_code ?? '') }}" pattern="[0-9]{2}-[0-9]{3}" required>
                @error('postal_code')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="street" class="form-label">Ulica</label>
                <input type="text" name="street" id="street" class="form-control" value="{{ old('street', $address->street ?? '') }}" required>
                @error('street')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="house_number" class="form-label">Numer domu</label>
                <input type="text" name="house_number" id="house_number" class="form-control" value="{{ old('house_number', $address->house_number ?? '') }}" required>
                @error('house_number')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="apartment_number" class="form-label">Numer mieszkania</label>
                <input type="text" name="apartment_number" id="apartment_number" class="form-control" value="{{ old('apartment_number', $address->apartment_number ?? '') }}">
                @error('apartment_number')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <button class="btn btn-success">Zapisz zmiany</button>
            <a href="{{ route('admin.manage.sellers.index') }}" class="btn btn-secondary">Anuluj</a>
        </form>
    </div>
@endsection
