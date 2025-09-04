<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $news->translations->first()->title ?? 'Bez názvu' }}
            </h2>
            <a href="{{ route('news.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                ← Zpět na novinky
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <article>
                        <header class="mb-6">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                                {{ $news->translations->first()->title ?? 'Bez názvu' }}
                            </h1>
                            
                            <div class="flex justify-between items-center text-sm text-gray-500 dark:text-gray-400">
                                <div>
                                    <span>Publikováno: {{ $news->published_at ? $news->published_at->format('d.m.Y H:i') : 'Nepublikováno' }}</span>
                                </div>
                                <div>
                                    <span>Autor: {{ $news->author->name ?? $news->author->email }}</span>
                                </div>
                            </div>
                        </header>
                        
                        <div class="prose dark:prose-invert max-w-none">
                            {!! nl2br(e($news->translations->first()->content ?? '')) !!}
                        </div>
                        
                        <footer class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('news.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                ← Zpět na seznam novinek
                            </a>
                        </footer>
                    </article>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
