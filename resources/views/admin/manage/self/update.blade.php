@extends('admin.panel')

@section('content')
    <div class="container mt-4">
        <h2>Edytuj swoje dane</h2>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.profile.update') }}">
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
                <label for="password" class="form-label">Nowe hasło (jeśli chcesz zmienić)</label>
                <input type="password" name="password" id="password" class="form-control">
                @error('password')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Powtórz nowe hasło</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
            </div>
            <button class="btn btn-success">Zapisz zmiany</button>
        </form>
    </div>
@endsection
