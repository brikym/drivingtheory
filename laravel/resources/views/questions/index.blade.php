@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Procházení otázek</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($categories as $category)
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-3">
                        {{ $category->translations->first()?->name ?? 'Kategorie ' . $category->code }}
                    </h2>
                    
                    @if($category->translations->first()?->description)
                        <p class="text-gray-600 mb-4">
                            {{ $category->translations->first()->description }}
                        </p>
                    @endif
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">
                            {{ $category->questions->count() }} otázek
                        </span>
                        
                        <a href="{{ route('questions.category', $category) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                            Zobrazit otázky
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    @if($categories->isEmpty())
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg">Žádné kategorie nebyly nalezeny.</p>
        </div>
    @endif
</div>
@endsection
