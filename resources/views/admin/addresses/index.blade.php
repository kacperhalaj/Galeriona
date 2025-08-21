@extends('admin.panel')

@section('content')
    <div class="container">
        <h1>Zarządzaj adresami</h1>

        <form method="GET" action="{{ route('admin.addresses.index') }}" class="mb-3">
            <div class="form-group">
                <label for="user_id">Wybierz użytkownika:</label>
                <select name="user_id" id="user_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Wybierz użytkownika --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ $selectedUser && $selectedUser->id == $user->id ? 'selected' : '' }}>
                            {{ $user->username }} ({{ $user->first_name }} {{ $user->last_name }})
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        @if ($selectedUser)
            <h2>Adresy dla: {{ $selectedUser->username }}</h2>
            <a href="{{ route('admin.addresses.create', ['user_id' => $selectedUser->id]) }}" class="btn btn-primary mb-3">Dodaj nowy adres</a>

            @if ($addresses->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Miasto</th>
                            <th>Kod pocztowy</th>
                            <th>Ulica</th>
                            <th>Numer domu</th>
                            <th>Numer mieszkania</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($addresses as $address)
                            <tr>
                                <td>{{ $address->city ?? '-'  }}</td>
                                <td>{{ $address->postal_code ?? '-'  }}</td>
                                <td>{{ $address->street ?? '-'  }}</td>
                                <td>{{ $address->house_number ?? '-'  }}</td>
                                <td>{{ $address->apartment_number ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('admin.addresses.edit', $address->id) }}" class="btn btn-sm btn-warning">Edytuj</a>
                                    <form action="{{ route('admin.addresses.destroy', $address->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy jesteś pewny?')">Usuń</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>Nie znaleziono żadnych adres dla podanego użytkownika.</p>
            @endif
        @else
            <p>Wybierz użytkownika dla którego chcesz zobaczyć adresy.</p>
        @endif
    </div>
@endsection
