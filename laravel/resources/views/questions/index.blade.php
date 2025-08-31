@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Procházení otázek</h1>
    
    {{-- Globální vyhledávací pole --}}
    <div class="mb-8">
        <form method="GET" action="{{ route('questions.index') }}" class="max-w-md">
            <div class="relative">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Hledat ve všech otázkách (min. 3 znaky, čeká 1s)..."
                       class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       minlength="3"
                       id="globalSearchInput"
                       autocomplete="off">
                <button type="submit" 
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
            @if(request('search'))
                <div class="mt-2">
                    <a href="{{ route('questions.index') }}" 
                       class="text-sm text-blue-600 hover:text-blue-800">
                        ✕ Zrušit vyhledávání
                    </a>
                </div>
            @endif
        </form>
    </div>

    {{-- Informace o výsledcích vyhledávání --}}
    @if(request('search'))
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-blue-800">
                <strong>Výsledky vyhledávání pro:</strong> "{{ request('search') }}"
                @if($categories->count() > 0)
                    <span class="text-blue-600">({{ $categories->count() }} {{ $categories->count() == 1 ? 'kategorie' : ($categories->count() < 5 ? 'kategorie' : 'kategorií') }})</span>
                @endif
            </p>
        </div>
    @endif
    
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
                            {{ $category->filtered_questions_count }} otázek
                        </span>
                        
                        <a href="{{ route('questions.category', array_merge(['category' => $category], request()->only('search'))) }}" 
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('globalSearchInput');
    const form = searchInput.closest('form');
    let searchTimeout;
    let isSubmitting = false;

    searchInput.addEventListener('input', function() {
        const value = this.value.trim();
        
        // Zrušit předchozí timeout
        clearTimeout(searchTimeout);
        
        // Pokud je méně než 3 znaky, nehledat
        if (value.length < 3 && value.length > 0) {
            return;
        }
        
        // Pokud je 3+ znaků nebo prázdné, hledat po 1000ms (1 sekunda)
        searchTimeout = setTimeout(() => {
            if (!isSubmitting) {
                isSubmitting = true;
                // Přidat indikátor načítání
                searchInput.style.opacity = '0.6';
                searchInput.placeholder = 'Vyhledávám...';
                form.submit();
            }
        }, 1000);
    });
    
    // Enter okamžitě vyhledá
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimeout);
            if (!isSubmitting) {
                isSubmitting = true;
                form.submit();
            }
        }
    });

    // Reset flag při načtení stránky
    window.addEventListener('pageshow', function() {
        isSubmitting = false;
        searchInput.style.opacity = '1';
        searchInput.placeholder = 'Hledat ve všech otázkách (min. 3 znaky, čeká 1s)...';
    });
});
</script>
@endsection
