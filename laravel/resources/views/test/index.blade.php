@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ __('app.test') }}</h1>
            <p class="text-xl text-gray-600">Vyberte skupinu</p>
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
                            {{ __('app.test') }} skupina: {{ $activeTest->vehicle_type }} 
                            - {{ $activeTest->getCurrentQuestionIndex() }}/{{ $activeTest->total_questions }} {{ __('app.question') }}
                        </p>
                        <p class="text-sm text-yellow-600 mt-1">
                            {{ __('app.remaining_time') }}: <span id="remaining-time">{{ gmdate('i:s', $activeTest->getRemainingTime()) }}</span>
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- A - Motocykl --}}
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="h-32 bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                    <span class="text-4xl font-bold text-white">A</span>
                </div>
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">A - Motocykl</h2>
                    <p class="text-gray-600 mb-4 text-sm">
                        Test pro řidičské oprávnění skupiny A
                    </p>
                    <form method="POST" action="{{ route('test.start') }}" class="w-full">
                        @csrf
                        <input type="hidden" name="vehicle_type" value="A">
                        <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200 text-sm">
                            Začít test A
                        </button>
                    </form>
                </div>
            </div>

            {{-- B - Osobní automobil --}}
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="h-32 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                    <span class="text-4xl font-bold text-white">B</span>
                </div>
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">B - Osobní automobil</h2>
                    <p class="text-gray-600 mb-4 text-sm">
                        Test pro řidičské oprávnění skupiny B
                    </p>
                    <form method="POST" action="{{ route('test.start') }}" class="w-full">
                        @csrf
                        <input type="hidden" name="vehicle_type" value="B">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200 text-sm">
                            Začít test B
                        </button>
                    </form>
                </div>
            </div>

            {{-- C - Nákladní automobil nad 3,5t --}}
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="h-32 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                    <span class="text-4xl font-bold text-white">C</span>
                </div>
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">C - Nákladní automobil nad 3,5t</h2>
                    <p class="text-gray-600 mb-4 text-sm">
                        Test pro řidičské oprávnění skupiny C
                    </p>
                    <form method="POST" action="{{ route('test.start') }}" class="w-full">
                        @csrf
                        <input type="hidden" name="vehicle_type" value="C">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200 text-sm">
                            Začít test C
                        </button>
                    </form>
                </div>
            </div>

            {{-- D - Autobus --}}
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="h-32 bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
                    <span class="text-4xl font-bold text-white">D</span>
                </div>
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">D - Autobus</h2>
                    <p class="text-gray-600 mb-4 text-sm">
                        Test pro řidičské oprávnění skupiny D
                    </p>
                    <form method="POST" action="{{ route('test.start') }}" class="w-full">
                        @csrf
                        <input type="hidden" name="vehicle_type" value="D">
                        <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200 text-sm">
                            Začít test D
                        </button>
                    </form>
                </div>
            </div>

            {{-- B+E - Osobní automobil s přívěsem --}}
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="h-32 bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center">
                    <span class="text-4xl font-bold text-white">B+E</span>
                </div>
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">B+E - Osobní automobil s přívěsem</h2>
                    <p class="text-gray-600 mb-4 text-sm">
                        Test pro řidičské oprávnění skupiny B+E
                    </p>
                    <form method="POST" action="{{ route('test.start') }}" class="w-full">
                        @csrf
                        <input type="hidden" name="vehicle_type" value="B+E">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200 text-sm">
                            Začít test B+E
                        </button>
                    </form>
                </div>
            </div>

            {{-- C+E - Nákladní automobil s přívěsem nad 750 kg --}}
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="h-32 bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center">
                    <span class="text-4xl font-bold text-white">C+E</span>
                </div>
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">C+E - Nákladní automobil s přívěsem nad 750 kg</h2>
                    <p class="text-gray-600 mb-4 text-sm">
                        Test pro řidičské oprávnění skupiny C+E
                    </p>
                    <form method="POST" action="{{ route('test.start') }}" class="w-full">
                        @csrf
                        <input type="hidden" name="vehicle_type" value="C+E">
                        <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200 text-sm">
                            Začít test C+E
                        </button>
                    </form>
                </div>
            </div>

            {{-- D+E - Autobus s přívěsem nad 750 kg --}}
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="h-32 bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center">
                    <span class="text-4xl font-bold text-white">D+E</span>
                </div>
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">D+E - Autobus s přívěsem nad 750 kg</h2>
                    <p class="text-gray-600 mb-4 text-sm">
                        Test pro řidičské oprávnění skupiny D+E
                    </p>
                    <form method="POST" action="{{ route('test.start') }}" class="w-full">
                        @csrf
                        <input type="hidden" name="vehicle_type" value="D+E">
                        <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200 text-sm">
                            Začít test D+E
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

@if($activeTest)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const remainingTimeElement = document.getElementById('remaining-time');
    if (!remainingTimeElement) return;

    // Počáteční čas z serveru
    const initialTime = {{ $activeTest->getRemainingTime() }};
    let remainingSeconds = initialTime;
    let timerExpired = false; // Flag pro kontrolu, zda už byl zobrazen alert

    function updateTimer() {
        if (remainingSeconds <= 0) {
            remainingTimeElement.textContent = '00:00';
            remainingTimeElement.style.color = 'red';
            remainingTimeElement.style.fontWeight = 'bold';
            
            // Zobrazit alert pouze jednou a ukončit test na serveru
            if (!timerExpired) {
                timerExpired = true;
                
                // Ukončit test na serveru
                fetch('{{ route("test.auto-complete", $activeTest) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(response => {
                    if (response.ok) {
                        alert('{{ __("app.time_expired_message") }}');
                        window.location.href = '{{ route("test.result") }}';
                    } else {
                        console.error('Chyba při ukončování testu');
                        alert('{{ __("app.time_expired_message") }}');
                        window.location.href = '{{ route("test.result") }}';
                    }
                }).catch(error => {
                    console.error('Chyba při ukončování testu:', error);
                    alert('{{ __("app.time_expired_message") }}');
                    window.location.href = '{{ route("test.result") }}';
                });
            }
            return;
        }

        const minutes = Math.floor(remainingSeconds / 60);
        const seconds = remainingSeconds % 60;
        
        const timeString = minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
        remainingTimeElement.textContent = timeString;

        // Změnit barvu když zbývá málo času
        if (remainingSeconds <= 300) { // 5 minut
            remainingTimeElement.style.color = 'red';
            remainingTimeElement.style.fontWeight = 'bold';
        } else if (remainingSeconds <= 600) { // 10 minut
            remainingTimeElement.style.color = 'orange';
            remainingTimeElement.style.fontWeight = 'bold';
        }

        remainingSeconds--;
    }

    // Aktualizovat každou sekundu
    updateTimer(); // Okamžitě zobrazit čas
    const timer = setInterval(updateTimer, 1000);

    // Vyčistit timer při opuštění stránky
    window.addEventListener('beforeunload', function() {
        clearInterval(timer);
    });
});
</script>
@endif
@endsection