@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Výsledky testu</h1>
            <p class="text-xl text-gray-600">Test pro {{ $test->vehicle_type == 'automobil' ? 'automobil' : 'motocykl' }}</p>
            
            @if($timeExpired)
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-800 font-medium">
                        ⏰ Časový limit 30 minut vypršel! Test byl automaticky ukončen.
                    </p>
                </div>
            @endif
        </div>

        {{-- Hlavní výsledek --}}
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <div class="text-center">
                @if($test->passed)
                    <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-green-600 mb-4">Test úspěšně dokončen!</h2>
                @else
                    <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-red-600 mb-4">Test nebyl úspěšný</h2>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ $test->earned_points }}</div>
                        <div class="text-gray-600">Získaných bodů</div>
                        <div class="text-sm text-gray-500">z {{ $test->total_points }} možných</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">{{ $testAnswers->where('is_correct', true)->count() }}</div>
                        <div class="text-gray-600">Správných odpovědí</div>
                        <div class="text-sm text-gray-500">z {{ $test->total_questions }} otázek</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold {{ $test->passed ? 'text-green-600' : 'text-red-600' }}">{{ round($test->percentage, 1) }}%</div>
                        <div class="text-gray-600">Úspěšnost</div>
                        <div class="text-sm text-gray-500">minimum 86%</div>
                    </div>
                </div>

                <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" 
                         style="width: {{ min($test->percentage, 100) }}%"></div>
                </div>
            </div>
        </div>

        {{-- Detailní výsledky --}}
        <div class="bg-white rounded-lg shadow-md p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">Detailní výsledky</h3>
            
            <div class="space-y-4">
                @foreach($testAnswers as $index => $testAnswer)
                    @php
                        $question = $testAnswer->question;
                        $isCorrect = $testAnswer->is_correct;
                    @endphp
                    <div class="border border-gray-200 rounded-lg p-4 {{ $isCorrect ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="font-semibold text-gray-900">
                                Otázka {{ $index + 1 }}: {{ $question->question_code }}
                            </h4>
                            <div class="text-right">
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full {{ $isCorrect ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $isCorrect ? 'Správně' : 'Špatně' }}
                                </span>
                                <div class="text-sm text-gray-500 mt-1">
                                    {{ $testAnswer->points_earned }}/{{ $testAnswer->points_possible }} bodů
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-gray-700 mb-2">{{ $question->translations->first()->text ?? 'N/A' }}</p>
                        
                        {{-- Media content pro otázku --}}
                        @if($question->mediaContent || $question->mediaContents->count() > 0)
                            <div class="mb-3">
                                @if($question->mediaContent)
                                    @if($question->mediaContent->media_type === 'image')
                                        <img src="{{ asset('storage/questions/images/' . $question->mediaContent->media_url) }}" 
                                             alt="Obrázek k otázce" 
                                             class="max-w-full h-auto rounded-lg shadow-sm"
                                             onerror="this.style.display='none'; console.log('Obrázek se nepodařilo načíst: ' + this.src);">
                                    @elseif($question->mediaContent->media_type === 'video')
                                        <video controls class="max-w-full h-auto rounded-lg shadow-sm">
                                            <source src="{{ asset('storage/questions/videos/' . $question->mediaContent->media_url) }}" type="video/mp4">
                                            Váš prohlížeč nepodporuje přehrávání videí.
                                        </video>
                                    @endif
                                @endif
                                
                                @foreach($question->mediaContents as $media)
                                    @if($media->media_type === 'image')
                                        <img src="{{ asset('storage/questions/images/' . $media->media_url) }}" 
                                             alt="Obrázek k otázce" 
                                             class="max-w-full h-auto rounded-lg shadow-sm mt-2"
                                             onerror="this.style.display='none'; console.log('Obrázek se nepodařilo načíst: ' + this.src);">
                                    @elseif($media->media_type === 'video')
                                        <video controls class="max-w-full h-auto rounded-lg shadow-sm mt-2">
                                            <source src="{{ asset('storage/questions/videos/' . $media->media_url) }}" type="video/mp4">
                                            Váš prohlížeč nepodporuje přehrávání videí.
                                        </video>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                        
                        <div class="text-sm">
                            <span class="font-medium">Vaše odpověď:</span> 
                            <span class="{{ $isCorrect ? 'text-green-600' : 'text-red-600' }}">
                                {{ $testAnswer->selectedAnswer->translations->first()->text ?? 'Neznámá odpověď' }}
                            </span>
                            
                            {{-- Media content pro vybranou odpověď --}}
                            @if($testAnswer->selectedAnswer->mediaContent || $testAnswer->selectedAnswer->mediaContents->count() > 0)
                                <div class="mt-2">
                                    @if($testAnswer->selectedAnswer->mediaContent)
                                        @if($testAnswer->selectedAnswer->mediaContent->media_type === 'image')
                                            <img src="{{ asset('storage/questions/images/' . $testAnswer->selectedAnswer->mediaContent->media_url) }}" 
                                                 alt="Obrázek k odpovědi" 
                                                 class="max-w-xs h-auto rounded-lg shadow-sm"
                                                 onerror="this.style.display='none'; console.log('Obrázek odpovědi se nepodařilo načíst: ' + this.src);">
                                        @elseif($testAnswer->selectedAnswer->mediaContent->media_type === 'video')
                                            <video controls class="max-w-xs h-auto rounded-lg shadow-sm">
                                                <source src="{{ asset('storage/questions/videos/' . $testAnswer->selectedAnswer->mediaContent->media_url) }}" type="video/mp4">
                                                Váš prohlížeč nepodporuje přehrávání videí.
                                            </video>
                                        @endif
                                    @endif
                                    
                                    @foreach($testAnswer->selectedAnswer->mediaContents as $media)
                                        @if($media->media_type === 'image')
                                            <img src="{{ asset('storage/questions/images/' . $media->media_url) }}" 
                                                 alt="Obrázek k odpovědi" 
                                                 class="max-w-xs h-auto rounded-lg shadow-sm mt-1"
                                                 onerror="this.style.display='none'; console.log('Obrázek odpovědi se nepodařilo načíst: ' + this.src);">
                                        @elseif($media->media_type === 'video')
                                            <video controls class="max-w-xs h-auto rounded-lg shadow-sm mt-1">
                                                <source src="{{ asset('storage/questions/videos/' . $media->media_url) }}" type="video/mp4">
                                                Váš prohlížeč nepodporuje přehrávání videí.
                                            </video>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        
                        @if(!$isCorrect)
                            <div class="text-sm mt-1">
                                <span class="font-medium">Správná odpověď:</span> 
                                @php
                                    $correctAnswer = $question->answers()->where('is_correct', true)->first();
                                @endphp
                                <span class="text-green-600">{{ $correctAnswer->translations->first()->text ?? 'Neznámá odpověď' }}</span>
                                
                                {{-- Media content pro správnou odpověď --}}
                                @if($correctAnswer && ($correctAnswer->mediaContent || $correctAnswer->mediaContents->count() > 0))
                                    <div class="mt-2">
                                        @if($correctAnswer->mediaContent)
                                            @if($correctAnswer->mediaContent->media_type === 'image')
                                                <img src="{{ asset('storage/questions/images/' . $correctAnswer->mediaContent->media_url) }}" 
                                                     alt="Obrázek ke správné odpovědi" 
                                                     class="max-w-xs h-auto rounded-lg shadow-sm"
                                                     onerror="this.style.display='none'; console.log('Obrázek správné odpovědi se nepodařilo načíst: ' + this.src);">
                                            @elseif($correctAnswer->mediaContent->media_type === 'video')
                                                <video controls class="max-w-xs h-auto rounded-lg shadow-sm">
                                                    <source src="{{ asset('storage/questions/videos/' . $correctAnswer->mediaContent->media_url) }}" type="video/mp4">
                                                    Váš prohlížeč nepodporuje přehrávání videí.
                                                </video>
                                            @endif
                                        @endif
                                        
                                        @foreach($correctAnswer->mediaContents as $media)
                                            @if($media->media_type === 'image')
                                                <img src="{{ asset('storage/questions/images/' . $media->media_url) }}" 
                                                     alt="Obrázek ke správné odpovědi" 
                                                     class="max-w-xs h-auto rounded-lg shadow-sm mt-1"
                                                     onerror="this.style.display='none'; console.log('Obrázek správné odpovědi se nepodařilo načíst: ' + this.src);">
                                            @elseif($media->media_type === 'video')
                                                <video controls class="max-w-xs h-auto rounded-lg shadow-sm mt-1">
                                                    <source src="{{ asset('storage/questions/videos/' . $media->media_url) }}" type="video/mp4">
                                                    Váš prohlížeč nepodporuje přehrávání videí.
                                                </video>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Akce --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8">
            <a href="{{ route('test.index') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-colors duration-200">
                Nový test
            </a>
            
            <a href="{{ route('test.history') }}" 
               class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg transition-colors duration-200">
                Historie testů
            </a>
            
            <a href="{{ route('questions.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-8 rounded-lg transition-colors duration-200">
                Procházet otázky
            </a>
        </div>
    </div>
</div>
@endsection
