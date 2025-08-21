@extends('client.panel')

@section('panel_content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
<div class="mt-4">
    <h5>Wesprzyj sprzedawcę</h5>
    <form method="POST" action="{{ route('client.donations.store') }}">
        @csrf
        <div class="mb-3">
            <label for="seller_id" class="form-label">Wybierz sprzedawcę</label>
            <select class="form-select" id="seller_id" name="seller_id" required>
                @foreach($sellers as $seller)
                    <option value="{{ $seller->id }}">{{ $seller->username }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="amount" class="form-label">Kwota darowizny (zł)</label>
            <input type="number" class="form-control" id="amount" name="amount" min="1" step="0.01" required>
        </div>
        <button type="submit" class="btn btn-primary">Wyślij darowiznę</button>
    </form>
    <hr>
    <h6>Ostatnie darowizny:</h6>
    <ul class="list-group">
        @foreach($recentDonations as $donation)
            <li class="list-group-item">
                Darowizna przekazana do: <b>{{ $donation->seller->username }}</b> – {{ number_format($donation->amount, 2, ',', ' ') }} zł ({{ $donation->created_at->format('d.m.Y H:i') }})
            </li>
        @endforeach
    </ul>
</div>
@endsection
