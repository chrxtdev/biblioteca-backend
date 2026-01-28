<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
    <h3 class="font-bold text-gray-900 dark:text-white mb-4">Progresso de Leitura</h3>
    <div class="space-y-3">
        @if(isset($readingProgress) && $readingProgress->count() > 0)
            @foreach($readingProgress as $progress)
                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="w-10 h-14 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                        @if($progress->book->cover_path)
                            <img src="{{ asset('storage/' . $progress->book->cover_path) }}" 
                                 alt="{{ $progress->book->title }}" 
                                 class="w-full h-full object-cover rounded">
                        @else
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-sm text-gray-900 dark:text-white line-clamp-1">{{ $progress->book->title }}</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $progress->book->author }}</p>
                        <div class="mt-2 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" 
                                 style="width: {{ $progress->progress_percentage }}%"></div>
                        </div>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                @if(is_null($progress->total_pages))
                                    Página {{ $progress->current_page }} de <span class="text-amber-500">calculando...</span>
                                @else
                                    Página {{ $progress->current_page }} de {{ $progress->total_pages }}
                                @endif
                            </span>
                            <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ $progress->progress_percentage }}%</span>
                        </div>
                        @if($progress->last_read_at)
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                Lido em {{ $progress->last_read_at->format('d/m/Y H:i') }}
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nenhuma leitura em andamento</h4>
                <p class="text-gray-500 dark:text-gray-400">Comece a ler um livro para ver seu progresso aqui.</p>
            </div>
        @endif
    </div>
</div>