@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold">Események</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-5">
        @foreach ($events as $event)
            <div
                class="bg-blue-200 rounded-lg shadow flex flex-col p-4 hover:scale-110 hover:shadow-xl shadow-gray-500 transition duration-200">
                <h2 class="text-lg font-semibold mb-2">{{ $event->title }}</h2>
                <p class="text-gray-700 text-sm flex-1">{{ $event->description }}</p>
                <p class="text-sm text-blue-500">Dátum: {{ $event->event_date->format('Y.m.d H:i') }}</p>
            </div>
        @endforeach
    </div>
@endsection
