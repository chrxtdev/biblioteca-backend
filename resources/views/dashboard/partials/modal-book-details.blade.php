@props(['book'])

<div x-show="showDetails" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
     style="display: none;"
     @keydown.escape.window="showDetails = false">

    <!-- Backdrop com Blur e Cor Dominante (Simulado) -->
    <div @click="showDetails = false" 
         class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <!-- Modal Content -->
    <div class="relative w-full max-w-4xl bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row max-h-[90vh]">
        
        <!-- Botão Fechar -->
        <button @click="showDetails = false" class="absolute top-4 right-4 z-20 p-2 bg-black/50 hover:bg-black/70 rounded-full text-white transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>

        <!-- Coluna Esquerda: Capa Imersiva -->
        <div class="w-full md:w-2/5 h-64 md:h-auto relative bg-gray-200 dark:bg-gray-800 shrink-0">
            <template x-if="selectedBook">
                <div class="w-full h-full relative group">
                    <!-- Imagem de Fundo (Blur) -->
                    <div class="absolute inset-0 bg-cover bg-center blur-xl opacity-50 dark:opacity-30" 
                         :style="`background-image: url('/storage/' + selectedBook.cover_path)`"></div>
                    
                    <!-- Imagem Principal -->
                    <div class="absolute inset-0 flex items-center justify-center p-8">
                        <img :src="'/storage/' + selectedBook.cover_path" 
                             class="w-auto h-full max-h-[80%] object-contain rounded-lg shadow-2xl transform transition-transform duration-500 group-hover:scale-105"
                             alt="Capa do Livro">
                    </div>

                    <!-- Gradiente Inferior -->
                    <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-black/60 to-transparent"></div>
                </div>
            </template>
        </div>

        <!-- Coluna Direita: Informações -->
        <div class="w-full md:w-3/5 p-8 flex flex-col overflow-y-auto bg-white dark:bg-gray-900 relative">
            
            <template x-if="selectedBook">
                <div class="flex-1 space-y-6">
                    <!-- Header -->
                    <div>
                        <!-- Badge de Categoria -->
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 mb-3" x-text="selectedBook.course"></span>
                        
                        <!-- Título -->
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white leading-tight font-serif" x-text="selectedBook.title"></h2>
                        
                        <!-- Autor e Metadados -->
                        <div class="flex flex-wrap items-center gap-4 mt-3 text-sm text-gray-500 dark:text-gray-400">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                <span x-text="selectedBook.author"></span>
                            </span>
                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                            <span class="flex items-center gap-1" x-show="selectedBook.total_pages">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                <span x-text="selectedBook.total_pages + ' páginas'"></span>
                            </span>
                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                            <span x-text="new Date(selectedBook.created_at).getFullYear()"></span>
                        </div>
                    </div>

                    <!-- Ações Principais -->
                    <div class="flex flex-col sm:flex-row gap-3 py-4 border-y border-gray-100 dark:border-gray-800">
                        <button @click="openReader(selectedBook); showDetails = false" 
                                class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold shadow-lg shadow-blue-500/30 transition-all hover:scale-[1.02] transform">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                            Ler Agora
                        </button>
                        
                        <button @click="toggleFavorite(selectedBook.id, isFavorited(selectedBook.id)).then(newState => {
                                    if(newState) favoriteBookIds.push(selectedBook.id);
                                    else favoriteBookIds = favoriteBookIds.filter(id => id !== selectedBook.id);
                                })" 
                                class="flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-medium transition-colors"
                                :class="isFavorited(selectedBook.id) 
                                    ? 'bg-red-500 hover:bg-red-600 text-white' 
                                    : 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-900 dark:text-white'">
                            
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" x-show="isFavorited(selectedBook.id)">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="!isFavorited(selectedBook.id)">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>

                            <span x-text="isFavorited(selectedBook.id) ? 'Favoritado' : 'Favoritar'"></span>
                        </button>
                    </div>

                    <!-- Sinopse -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Sinopse</h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-sm md:text-base" x-text="selectedBook.description || 'Sem descrição disponível.'"></p>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
