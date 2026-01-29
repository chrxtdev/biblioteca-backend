@props(['book', 'readingProgress', 'favoriteBookIds'])

@php
    $progress = $readingProgress->firstWhere('book_id', $book->id);
    $isFavorited = in_array($book->id, $favoriteBookIds);
@endphp

<div x-data="{ isFavorited: {{ $isFavorited ? 'true' : 'false' }} }" 
     class="group"
     :class="{ 'hover:scale-105': isLoaded }"
     :style="isLoaded ? 'transition: transform 300ms ease-in-out' : ''">
    <div class="relative w-full aspect-[3/4] bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700"
         :style="isLoaded ? 'transition: box-shadow 300ms ease-in-out' : ''"
    >

        {{-- Imagem ou Placeholder --}}
        <div @click="openReader({{ json_encode($book) }})" class="absolute inset-0 cursor-pointer">
            @if($book->cover_path)
                <img src="{{ asset('storage/' . $book->cover_path) }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
            @else
                <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                    <svg class="w-16 h-16 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span class="text-sm font-medium text-center">Sem capa</span>
                </div>
            @endif
        </div>

        {{-- Botão de Favorito --}}
        <button @click.stop="toggleFavorite({{ $book->id }}, isFavorited).then(newState => isFavorited = newState)"
                class="absolute top-2 right-2 z-10 p-1.5 bg-black/30 rounded-full text-white hover:bg-red-500/80 transition-colors duration-200"
                :class="{ 'bg-red-500 text-white': isFavorited }">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
        </button>

        {{-- Overlay de Informações (visível no hover) --}}
        <div @click="openReader({{ json_encode($book) }})" class="cursor-pointer absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <div class="absolute bottom-0 left-0 right-0 p-3 text-white">
                <h4 class="font-semibold text-sm line-clamp-2 mb-1">{{ $book->title }}</h4>
                <p class="text-xs opacity-90">{{ $book->author }}</p>
            </div>
        </div>
    </div>

    {{-- Informações abaixo do card --}}
    <div class="mt-2 px-1">
        <h4 class="font-medium text-sm text-gray-900 dark:text-white line-clamp-1">{{ $book->title }}</h4>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $book->author }}</p>

        {{-- Barra de Progresso ou Status de Processamento --}}
        @if(is_null($book->total_pages))
            {{-- PDF ainda sendo processado --}}
            <div class="mt-2 flex items-center gap-1.5 text-xs text-amber-600 dark:text-amber-400">
                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span>Calculando páginas...</span>
            </div>
        @elseif($progress && $book->total_pages > 0)
            <div class="mt-2">
                <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 w-full">
                    <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ ($progress->current_page / $book->total_pages) * 100 }}%"></div>
                </div>
                <div class="flex justify-between items-center mt-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        Pág. {{ $progress->current_page }} de {{ $book->total_pages }}
                    </span>
                    <span class="text-xs font-bold text-green-600 dark:text-green-400">
                        {{ floor(($progress->current_page / $book->total_pages) * 100) }}%
                    </span>
                </div>
            </div>
        @endif
    </div>
</div>
