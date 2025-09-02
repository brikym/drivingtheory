@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        {{-- Navigační panel a čas --}}
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">{{ __('app.question') }} {{ $currentQuestionIndex + 1 }} {{ __('app.of') }} {{ count($questions) }}</span>
                    @php
                        $answeredCount = $test->testAnswers()->count();
                        $totalCount = count($questions);
                    @endphp
                    <span class="text-sm text-gray-600">
                        {{ __('app.answered') }}: <span class="font-medium text-green-600">{{ $answeredCount }}</span>/{{ $totalCount }}
                    </span>
                </div>
                <div class="text-right">
                    <div class="text-sm font-medium" id="remainingTime">
                        {{ __('app.remaining_time') }}: <span id="timeDisplay">{{ floor($remainingTime / 60) }}:{{ str_pad($remainingTime % 60, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
            </div>
            
            {{-- Navigační panel s čísly otázek --}}
            <div class="bg-white rounded-lg shadow-sm border p-4">
                <div class="flex flex-wrap gap-2 justify-center">
                    @for($i = 1; $i <= count($questions); $i++)
                        @php
                            $isCurrent = $i == ($currentQuestionIndex + 1);
                            // Zkontrolovat, zda je otázka zodpovězena pomocí databáze
                            $isAnswered = $test->testAnswers()->where('question_id', $questions[$i-1]['id'])->exists();
                        @endphp
                        <a href="{{ route('test.question', ['q' => $i]) }}" 
                           class="w-10 h-10 flex items-center justify-center rounded-lg border-2 transition-all duration-200
                                  {{ $isCurrent ? 'bg-blue-600 text-white border-blue-600' : 
                                     ($isAnswered ? 'bg-green-100 text-green-800 border-green-300 hover:bg-green-200' : 
                                      'bg-gray-50 text-gray-600 border-gray-300 hover:bg-gray-100') }}">
                            {{ $i }}
                        </a>
                    @endfor
                </div>
            </div>
        </div>

        {{-- Otázka --}}
        <div class="bg-white rounded-lg shadow-md p-8 mb-6">
            <div class="flex items-start justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    Otázka {{ $currentQuestion['question_code'] }}
                </h2>
                <div class="text-right">
                    <span class="inline-block bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                        {{ $currentQuestion['points'] }} {{ $currentQuestion['points'] == 1 ? 'bod' : ($currentQuestion['points'] < 5 ? 'body' : 'bodů') }}
                    </span>
                    <div class="text-sm text-gray-500 mt-1">
                        {{ ucfirst(str_replace('_', ' ', $currentQuestion['section'])) }}
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-lg text-gray-700 leading-relaxed">
                    {{ $currentQuestion['text'] }}
                </p>
            </div>

            @if($currentQuestion['media_content'])
                <div class="mb-6">
                    <h4 class="font-medium text-gray-800 mb-2">Obrázek/video k otázce:</h4>
                    @if($currentQuestion['media_content']->media_type === 'image')
                        <img src="{{ asset('storage/questions/images/' . $currentQuestion['media_content']->media_url) }}" 
                             alt="Obrázek k otázce" 
                             class="max-w-full h-auto rounded-md">
                    @elseif($currentQuestion['media_content']->media_type === 'video')
                        <video controls class="max-w-full h-auto rounded-md">
                            <source src="{{ asset('storage/questions/videos/' . $currentQuestion['media_content']->media_url) }}" type="video/mp4">
                            Váš prohlížeč nepodporuje přehrávání videí.
                        </video>
                    @endif
                </div>
            @endif

            {{-- Odpovědi --}}
            <div id="question-form" class="space-y-4">
                <input type="hidden" id="question_id" value="{{ $currentQuestion['id'] }}">
                <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">
                
                @foreach($currentQuestion['answers'] as $answer)
                    <label class="flex items-start space-x-4 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors duration-200 answer-option" 
                           data-answer-id="{{ $answer['id'] }}">
                        <input type="radio" 
                               name="answer_id" 
                               value="{{ $answer['id'] }}" 
                               class="mt-1 w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                               {{ $selectedAnswerId == $answer['id'] ? 'checked' : '' }}
                               required>
                        <div class="flex-1">
                            <span class="text-gray-700">{{ $answer['text'] }}</span>
                            
                            @if($answer['media_content'])
                                <div class="mt-2">
                                    @if($answer['media_content']->media_type === 'image')
                                        <img src="{{ asset('storage/questions/images/' . $answer['media_content']->media_url) }}" 
                                             alt="Obrázek k odpovědi" 
                                             class="max-w-full h-auto rounded-md max-h-32">
                                    @elseif($answer['media_content']->media_type === 'video')
                                        <video controls class="max-w-full h-auto rounded-md max-h-32">
                                            <source src="{{ asset('storage/questions/videos/' . $answer['media_content']->media_url) }}" type="video/mp4">
                                            Váš prohlížeč nepodporuje přehrávání videí.
                                        </video>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </label>
                @endforeach

                <div class="flex justify-between items-center pt-6">
                    <div class="flex space-x-4">
                        {{-- Předchozí otázka --}}
                        @if($currentQuestionIndex > 0)
                            <a href="{{ route('test.question', ['q' => $currentQuestionIndex]) }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                                ← {{ __('app.previous_question') }}
                            </a>
                        @endif
                    </div>
                    
                    <div class="flex space-x-4">
                        {{-- Další otázka --}}
                        @if($currentQuestionIndex + 1 < count($questions))
                            <a href="{{ route('test.question', ['q' => $currentQuestionIndex + 2]) }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                                {{ __('app.next_question') }} →
                            </a>
                        @endif
                        
                        @php
                            $totalQuestions = count($questions);
                            $answeredQuestions = $test->testAnswers()->count();
                            $allQuestionsAnswered = $answeredQuestions >= $totalQuestions;
                        @endphp
                        
                        {{-- Dokončit test --}}
                        @if($allQuestionsAnswered)
                            <button type="button" 
                                    id="finish-test-btn"
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg transition-colors duration-200">
                                {{ __('app.finish_test') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let remainingTime = {{ $remainingTime }};
    const timeDisplay = document.getElementById('timeDisplay');
    const remainingTimeDiv = document.getElementById('remainingTime');
    
    function updateTime() {
        if (remainingTime <= 0) {
            // Čas vypršel, přesměrovat na výsledky
            window.location.href = '{{ route("test.result") }}';
            return;
        }
        
        const minutes = Math.floor(remainingTime / 60);
        const seconds = remainingTime % 60;
        timeDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        // Změnit barvu při nízkém čase
        if (remainingTime <= 300) { // 5 minut
            remainingTimeDiv.classList.add('text-red-600');
            remainingTimeDiv.classList.remove('text-gray-600');
        } else if (remainingTime <= 600) { // 10 minut
            remainingTimeDiv.classList.add('text-yellow-600');
            remainingTimeDiv.classList.remove('text-gray-600');
        }
        
        remainingTime--;
    }
    
    // Aktualizovat čas každou sekundu
    updateTime();
    setInterval(updateTime, 1000);

    // Automatické ukládání odpovědí
    const answerOptions = document.querySelectorAll('.answer-option');
    const questionId = document.getElementById('question_id').value;
    const csrfToken = document.getElementById('csrf_token').value;
    
    answerOptions.forEach(option => {
        const radioInput = option.querySelector('input[type="radio"]');
        
        radioInput.addEventListener('change', function() {
            if (this.checked) {
                const answerId = this.value;
                
                // Odeslat odpověď na server
                fetch('{{ route("test.answer") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        answer_id: answerId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Odpověď byla uložena, přesměrovat na další otázku
                        setTimeout(() => {
                            const currentQuestionNumber = {{ $currentQuestionIndex + 1 }};
                            
                            if (data.all_answered) {
                                // Všechny otázky zodpovězeny, zobrazit tlačítko dokončit
                                window.location.reload();
                            } else if (currentQuestionNumber < data.total_questions) {
                                // Přesměrovat na další otázku
                                window.location.href = '{{ route("test.question") }}?q=' + (currentQuestionNumber + 1);
                            } else {
                                // Jsme na poslední otázce, ale ne všechny jsou zodpovězeny
                                // Najít první nezodpovězenou otázku
                                window.location.href = '{{ route("test.question") }}?q=1';
                            }
                        }, 500);
                    }
                })
                .catch(error => {
                    console.error('Chyba při ukládání odpovědi:', error);
                });
            }
        });
    });

    // Dokončit test
    const finishBtn = document.getElementById('finish-test-btn');
    if (finishBtn) {
        finishBtn.addEventListener('click', function() {
            if (confirm('{{ __("app.confirm") }}')) {
                // Odeslat POST požadavek pro dokončení testu
                fetch('{{ route("test.finish") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        window.location.href = '{{ route("test.result") }}';
                    } else {
                        alert('Chyba při dokončování testu');
                    }
                })
                .catch(error => {
                    console.error('Chyba při dokončování testu:', error);
                    alert('Chyba při dokončování testu');
                });
            }
        });
    }
});
</script>
@endsection
