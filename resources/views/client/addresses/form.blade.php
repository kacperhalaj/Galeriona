@extends('client.panel')

@section('panel_content')
<div class="card mt-4">
    <div class="card-header">{{ isset($address) ? 'Edytuj Adres' : 'Dodaj Nowy Adres' }}</div>
    <div class="card-body">
        <form action="{{ isset($address) ? route('client.addresses.update', $address->id) : route('client.addresses.store') }}" method="POST">
            @csrf
            @if (isset($address))
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="city" class="form-label">Miasto *</label>
                <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $address->city ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label for="street" class="form-label">Ulica *</label>
                <input type="text" class="form-control" id="street" name="street" value="{{ old('street', $address->street ?? '') }}" required>
            </div> 
            <div class="mb-3">
                <label for="postal_code" class="form-label">Kod pocztowy *</label>
                <input type="text" class="form-control" id="postal_code" name="postal_code" placeholder="00-000" value="{{ old('postal_code', $address->postal_code ?? '') }}" pattern="[0-9]{2}-[0-9]{3}" title="Format: XX-XXX (np. 00-123)" required>
            </div>


            <div class="mb-3">
                <label for="postal_code" class="form-label">Numer domu *</label>
                <input type="text" class="form-control" id="house_number" name="house_number" value="{{ old('house_number', $address->house_number ?? '') }}" required>
            </div>

                <div class="mb-3">
                <label for="postal_code" class="form-label">Numer mieszkania</label>
                <input type="text" class="form-control" id="apartment_number" name="apartment_number" value="{{ old('apartment_number', $address->apartment_number ?? '') }}">
            </div>
        

            <button type="submit" class="btn btn-primary">{{ isset($address) ? 'Zapisz zmiany' : 'Dodaj adres' }}</button>
            <a href="{{ route('client.addresses.index') }}" class="btn btn-secondary">Anuluj</a>
        </form>
    </div>
</div>


<script>
        // Kod pocztowy - formatowanie
        document.getElementById('postal_code').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '-' + value.substring(2, 5);
            }
            this.value = value;
        });
</script>
@endsection