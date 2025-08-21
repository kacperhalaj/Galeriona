@extends('admin.panel')

@section('content')
    <div class="container mt-4">
        <h2>Szczegóły klienta</h2>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $user->username }}</h5>
                <p class="card-text"><strong>Email:</strong> {{ $user->email }}</p>
                <p class="card-text"><strong>Imię:</strong> {{ $user->first_name }}</p>
                <p class="card-text"><strong>Nazwisko:</strong> {{ $user->last_name }}</p>
                @php $address = $user->addresses->first(); @endphp
                <p class="card-text"><strong>Miasto:</strong> {{ $address->city ?? '-' }}</p>
                <p class="card-text"><strong>Kod pocztowy:</strong> {{ $address->postal_code ?? '-' }}</p>
                <p class="card-text"><strong>Ulica:</strong> {{ $address->street ?? '-' }}</p>
                <p class="card-text"><strong>Numer domu:</strong> {{ $address->house_number ?? '-' }}</p>
                <p class="card-text"><strong>Numer mieszkania:</strong> {{ $address->apartment_number ?? '-' }}</p>
                <p class="card-text"><strong>Data rejestracji:</strong> {{ $user->created_at }}</p>
                <p class="card-text"><strong>ID:</strong> {{ $user->id }}</p>
            </div>
        </div>
        <a href="{{ route('admin.manage.users.edit', $user) }}" class="btn btn-warning mt-3">Edytuj</a>
        <a href="{{ route('admin.manage.users.index') }}" class="btn btn-secondary mt-3">Powrót do listy</a>
    </div>
@endsection
