<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Upravit novinku
            </h2>
            <a href="{{ route('admin.news.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                ← Zpět na seznam
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.news.update', $news) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Czech Version -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                    Česká verze
                                </h3>
                                
                                <div>
                                    <label for="title_cs" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Název *
                                    </label>
                                    <input type="text" name="title_cs" id="title_cs" 
                                           value="{{ old('title_cs', $news->translations->where('locale', 'cs')->first()->title ?? '') }}" required
                                           class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('title_cs')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="content_cs" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Obsah *
                                    </label>
                                    <textarea name="content_cs" id="content_cs" rows="12" required
                                              class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('content_cs', $news->translations->where('locale', 'cs')->first()->content ?? '') }}</textarea>
                                    @error('content_cs')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- English Version -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                    English version
                                </h3>
                                
                                <div>
                                    <label for="title_en" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Title *
                                    </label>
                                    <input type="text" name="title_en" id="title_en" 
                                           value="{{ old('title_en', $news->translations->where('locale', 'en')->first()->title ?? '') }}" required
                                           class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('title_en')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="content_en" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Content *
                                    </label>
                                    <textarea name="content_en" id="content_en" rows="12" required
                                              class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('content_en', $news->translations->where('locale', 'en')->first()->content ?? '') }}</textarea>
                                    @error('content_en')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-8 space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_published" id="is_published" value="1" 
                                       {{ old('is_published', $news->is_published) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_published" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                    Publikovat
                                </label>
                            </div>
                            
                            <div id="publish_date_container" class="{{ old('is_published', $news->is_published) ? '' : 'hidden' }}">
                                <label for="published_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Datum publikace
                                </label>
                                <input type="datetime-local" name="published_at" id="published_at" 
                                       value="{{ old('published_at', $news->published_at ? $news->published_at->format('Y-m-d\TH:i') : '') }}"
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="mt-8 flex justify-end space-x-4">
                            <a href="{{ route('admin.news.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Zrušit
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Uložit změny
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('is_published').addEventListener('change', function() {
            const container = document.getElementById('publish_date_container');
            if (this.checked) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
