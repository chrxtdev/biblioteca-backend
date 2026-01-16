<!-- Recommended Section -->
<div class="mb-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Recommended</h2>
        <button class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">
            Mostrar tudo
        </button>
    </div>
    
    <div class="horizontal-scroll overflow-x-auto pb-4">
        <div class="flex gap-6" style="min-width: max-content;">
            @if(!$books->isEmpty())
                @foreach($books->take(8) as $book)
                    <div class="book-card flex-shrink-0 cursor-pointer" 
                         @click="openReader('{{ asset('storage/' . $book->file_path) }}', '{{ $book->title }}', {{ $book->id }})">
                        <div class="w-32">
                            @if($book->cover_path)
                                <img src="{{ asset('storage/' . $book->cover_path) }}" 
                                     alt="{{ $book->title }}" 
                                     class="w-full h-48 object-cover rounded-lg shadow-md">
                            @else
                                <div class="w-full h-48 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 rounded-lg shadow-md flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            @endif
                            <h3 class="mt-3 font-semibold text-sm text-gray-900 dark:text-white line-clamp-1">{{ $book->title }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $book->author }}</p>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>Nenhum livro foi encontrado. Tente ajustar sua busca.</p>
                </div>
            @endif
        </div>
    </div>
</div>
