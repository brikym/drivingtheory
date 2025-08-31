@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('questions.index', request()->only('search')) }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
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

    {{-- Informace o výsledcích vyhledávání --}}
    @if($search)
        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-blue-800">
                <strong>Výsledky vyhledávání pro:</strong> "{{ $search }}"
                @if($questions->total() > 0)
                    <span class="text-blue-600">({{ $questions->total() }} {{ $questions->total() == 1 ? 'výsledek' : ($questions->total() < 5 ? 'výsledky' : 'výsledků') }})</span>
                @endif
            </p>
        </div>
    @endif
    
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
                        @if($search && $question->translations->first()?->text)
                            {!! str_ireplace($search, '<mark class="bg-yellow-200 px-1 rounded">' . $search . '</mark>', $question->translations->first()->text) !!}
                        @else
                            {{ $question->translations->first()?->text ?? 'Text otázky není k dispozici' }}
                        @endif
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
                                        @if($search && $answer->translations->first()?->text)
                                            {!! str_ireplace($search, '<mark class="bg-yellow-200 px-1 rounded">' . $search . '</mark>', $answer->translations->first()->text) !!}
                                        @else
                                            {{ $answer->translations->first()?->text ?? 'Text odpovědi není k dispozici' }}
                                        @endif
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

    {{-- Navigace stránkování --}}
    @if($questions->hasPages())
        <div class="mt-8">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Zobrazeno {{ $questions->firstItem() ?? 0 }} až {{ $questions->lastItem() ?? 0 }} 
                    z celkem {{ $questions->total() }} otázek
                </div>
                
                <div class="flex space-x-2">
                    {{-- Předchozí stránka --}}
                    @if($questions->onFirstPage())
                        <span class="px-3 py-2 text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">
                            ← Předchozí
                        </span>
                    @else
                        <a href="{{ $questions->previousPageUrl() }}" 
                           class="px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                            ← Předchozí
                        </a>
                    @endif

                    {{-- Čísla stránek --}}
                    <div class="flex space-x-1">
                        @php
                            $currentPage = $questions->currentPage();
                            $lastPage = $questions->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                            
                            // Pokud jsme blízko začátku, zobrazíme více stránek na konci
                            if ($currentPage <= 3) {
                                $end = min($lastPage, 5);
                            }
                            
                            // Pokud jsme blízko konce, zobrazíme více stránek na začátku
                            if ($currentPage > $lastPage - 3) {
                                $start = max(1, $lastPage - 4);
                            }
                        @endphp

                        {{-- První stránka --}}
                        @if($start > 1)
                            <a href="{{ $questions->url(1) }}" 
                               class="px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                1
                            </a>
                            @if($start > 2)
                                <span class="px-3 py-2 text-gray-500">...</span>
                            @endif
                        @endif

                        {{-- Střední stránky --}}
                        @for($page = $start; $page <= $end; $page++)
                            @if($page == $currentPage)
                                <span class="px-3 py-2 text-white bg-blue-600 rounded-md">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $questions->url($page) }}" 
                                   class="px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor

                        {{-- Poslední stránka --}}
                        @if($end < $lastPage)
                            @if($end < $lastPage - 1)
                                <span class="px-3 py-2 text-gray-500">...</span>
                            @endif
                            <a href="{{ $questions->url($lastPage) }}" 
                               class="px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                {{ $lastPage }}
                            </a>
                        @endif
                    </div>

                    {{-- Další stránka --}}
                    @if($questions->hasMorePages())
                        <a href="{{ $questions->nextPageUrl() }}" 
                           class="px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                            Další →
                        </a>
                    @else
                        <span class="px-3 py-2 text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">
                            Další →
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
