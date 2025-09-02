@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ __('app.test') }}</h1>
            <p class="text-xl text-gray-600">{{ __('app.select_vehicle_type') }}</p>
        </div>

        @php
            $activeTest = Auth::user()->getActiveTest();
        @endphp

        @if($activeTest)
            {{-- Aktivní test --}}
            <div class="mb-8 bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-yellow-800 mb-2">
                            {{ __('app.in_progress') }}
                        </h3>
                        <p class="text-yellow-700">
                            {{ __('app.test') }} {{ __('app.vehicle_type') }}: {{ $activeTest->vehicle_type == 'automobil' ? __('app.automobile') : __('app.motorcycle') }} 
                            - {{ $activeTest->getCurrentQuestionIndex() }}/{{ $activeTest->total_questions }} {{ __('app.question') }}
                        </p>
                        <p class="text-sm text-yellow-600 mt-1">
                            {{ __('app.remaining_time') }}: {{ gmdate('i:s', $activeTest->getRemainingTime()) }}
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('test.question') }}" 
                           class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                            {{ __('app.continue_test') }}
                        </a>
                        <form method="POST" action="{{ route('test.cancel', $activeTest) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200"
                                    onclick="return confirm('{{ __("app.confirm_delete") }}')">
                                {{ __('app.cancel_test') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if(!$activeTest)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Motocykl --}}
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="h-48 bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                    <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                    </svg>
                </div>
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('app.motorcycle') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('app.test') }} {{ __('app.motorcycle') }}
                    </p>
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Otázky o bezpečnosti motocyklu
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Pravidla silničního provozu
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Technické znalosti
                        </div>
                    </div>
                    <form method="POST" action="{{ route('test.start') }}" class="w-full">
                        @csrf
                        <input type="hidden" name="vehicle_type" value="motocykl">
                        <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200">
                            {{ __('app.start_test') }} - {{ __('app.motorcycle') }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Automobil --}}
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="h-48 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                    <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.22.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                    </svg>
                </div>
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('app.automobile') }}</h2>
                    <p class="text-gray-600 mb-6">
                        {{ __('app.test') }} {{ __('app.automobile') }}
                    </p>
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Dopravní předpisy
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Bezpečnost jízdy
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Technické znalosti vozidla
                        </div>
                    </div>
                    <form method="POST" action="{{ route('test.start') }}" class="w-full">
                        @csrf
                        <input type="hidden" name="vehicle_type" value="automobil">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200">
                            {{ __('app.start_test') }} - {{ __('app.automobile') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- Informace o testu --}}
        <div class="mt-12 bg-gray-50 rounded-xl p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Informace o testu</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Časový limit</h4>
                    <p class="text-gray-600">30 minut na dokončení testu</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Počet otázek</h4>
                    <p class="text-gray-600">25 náhodně vybraných otázek</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Minimální úspěšnost</h4>
                    <p class="text-gray-600">43 správných odpovědí (86%)</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
