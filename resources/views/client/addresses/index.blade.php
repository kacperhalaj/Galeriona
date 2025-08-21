@extends('client.panel')

@section('panel_content')
<div class="card mt-4">
    <div class="card-header">Zarządzanie Adresami</div>
    <div class="card-body">
        <a href="{{ route('client.addresses.create') }}" class="btn btn-primary mb-3">Dodaj nowy adres</a>
        <table class="table">
            <thead>
                <tr>
                    <th>Miasto</th>
                    <th>Ulica</th>
                    <th>Kod pocztowy</th>
                    <th>Numer domu</th>
                    <th>Numer mieszkania</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($addresses as $address)
                    <tr>
                        <td>{{ $address->city }}</td>
                        <td>{{ $address->street }}</td>
                        <td>{{ $address->postal_code }}</td>
                        <td>{{ $address->house_number }}</td>
                        <td>{{ $address->apartment_number }}</td>
                        
                        <td>
                            <a href="{{ route('client.addresses.edit', $address->id) }}" class="btn btn-sm btn-warning">Edytuj</a>
                            <form action="{{ route('client.addresses.destroy', $address->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Czy na pewno chcesz usunąć ten adres?')">Usuń</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Nie masz jeszcze żadnych zapisanych adresów.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
