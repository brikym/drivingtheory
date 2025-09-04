<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Novinky
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($news->count() > 0)
                        <div class="space-y-6">
                            @foreach($news as $item)
                                <article class="border-b border-gray-200 dark:border-gray-700 pb-6 last:border-b-0">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                            <a href="{{ route('news.show', $item->id) }}">
                                                {{ $item->translations->first()->title ?? 'Bez názvu' }}
                                            </a>
                                        </h3>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $item->published_at ? $item->published_at->format('d.m.Y H:i') : 'Nepublikováno' }}
                                        </span>
                                    </div>
                                    
                                    <div class="text-gray-600 dark:text-gray-300 mb-3">
                                        @php
                                            $content = $item->translations->first()->content ?? '';
                                            $excerpt = Str::limit(strip_tags($content), 200);
                                        @endphp
                                        {{ $excerpt }}
                                    </div>
                                    
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            Autor: {{ $item->author->name ?? $item->author->email }}
                                        </span>
                                        <a href="{{ route('news.show', $item->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                            Číst více →
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                        
                        <div class="mt-6">
                            {{ $news->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">Zatím nejsou k dispozici žádné novinky.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
