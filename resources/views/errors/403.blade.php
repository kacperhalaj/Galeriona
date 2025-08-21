@extends('errors::minimal')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Brak uprawnień do dostępu do tej strony.'))

@section('button')
    <div class="text-center mt-8">
        <a href="{{ url('/') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">{{ __('Powrót do głównej strony') }}</a>
    </div>
@endsection
