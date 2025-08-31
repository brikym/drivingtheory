@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('questions.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
            ← Zpět na kategorie
        </a>
        
        <h1 class="text-3xl font-bold text-gray-900">
            {{ $category->translations->first()?->name ?? 'Kategorie ' . $category->code }}
        </h1>
        
        @if($category->translations->first()?->description)
            <p class="text-gray-600 mt-2">
                {{ $category->translations->first()->description }}
            </p>
        @endif
    </div>
    
    <div class="space-y-6">
        @foreach($questions as $question)
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Otázka {{ $question->question_code }}
                    </h3>
                    <span class="text-sm text-gray-500">
                        {{ $question->points_count }} bodů
                    </span>
                </div>
                
                <div class="mb-4">
                    <p class="text-gray-700 leading-relaxed">
                        {{ $question->translations->first()?->text ?? 'Text otázky není k dispozici' }}
                    </p>
                </div>
                
                @if($question->mediaContent)
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-800 mb-2">Obrázek/video k otázce:</h4>
                        @if($question->mediaContent->media_type === 'image')
                            <img src="{{ asset('storage/questions/images/' . $question->mediaContent->media_url) }}" 
                                 alt="Obrázek k otázce" 
                                 class="max-w-full h-auto rounded-md"
                                 onerror="this.style.display='none'; console.log('Obrázek se nepodařilo načíst: ' + this.src);">
                        @elseif($question->mediaContent->media_type === 'video')
                            <video controls class="max-w-full h-auto rounded-md">
                                <source src="{{ asset('storage/questions/videos/' . $question->mediaContent->media_url) }}" type="video/mp4">
                                Váš prohlížeč nepodporuje přehrávání videí.
                            </video>
                        @endif
                    </div>
                @endif
                
                <div class="space-y-3">
                    <h4 class="font-medium text-gray-800">Možné odpovědi:</h4>
                    @foreach($question->answers as $answer)
                        <div class="p-4 bg-gray-50 rounded-md">
                            <div class="flex items-start space-x-3">
                                <span class="w-4 h-4 rounded-full border-2 border-gray-300 flex-shrink-0 mt-1"></span>
                                <div class="flex-1">
                                    <span class="text-gray-700">
                                        {{ $answer->translations->first()?->text ?? 'Text odpovědi není k dispozici' }}
                                    </span>
                                    
                                    @if($answer->mediaContent)
                                        <div class="mt-2">
                                            @if($answer->mediaContent->media_type === 'image')
                                                <img src="{{ asset('storage/questions/images/' . $answer->mediaContent->media_url) }}" 
                                                     alt="Obrázek k odpovědi" 
                                                     class="max-w-full h-auto rounded-md max-h-32"
                                                     onerror="this.style.display='none'; console.log('Obrázek odpovědi se nepodařilo načíst: ' + this.src);">
                                            @elseif($answer->mediaContent->media_type === 'video')
                                                <video controls class="max-w-full h-auto rounded-md max-h-32">
                                                    <source src="{{ asset('storage/questions/videos/' . $answer->mediaContent->media_url) }}" type="video/mp4">
                                                    Váš prohlížeč nepodporuje přehrávání videí.
                                                </video>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    
    @if($questions->isEmpty())
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg">V této kategorii nebyly nalezeny žádné otázky.</p>
        </div>
    @endif
</div>
@endsection
