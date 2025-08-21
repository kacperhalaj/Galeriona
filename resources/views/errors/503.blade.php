@extends('errors::minimal')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('Usługa niedostępna'))

@section('button')
    <div class="text-center mt-8">
        <a href="{{ url('/') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">{{ __('Powrót do głównej strony') }}</a>
    </div>
@endsection
