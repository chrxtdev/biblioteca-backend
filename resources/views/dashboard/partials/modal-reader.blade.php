<!-- Modal Leitor de PDF -->
<div x-show="showReader" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    @keydown.escape.window="closeReader()"
    x-init="$watch('showReader', value => { 
        if(value) { 
            document.body.classList.add('overflow-hidden'); 
        } else { 
            document.body.classList.remove('overflow-hidden'); 
        } 
    })"
    class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center" x-cloak>

    <div @click.outside="closeReader()"
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full h-[98vh] flex flex-col mx-2 overflow-hidden transition-all duration-300"
        :class="{
            'max-w-full rounded-none': isFullscreen,
            'max-w-7xl': viewMode === 'double' && !isFullscreen,
            'max-w-6xl': viewMode !== 'double' && !isFullscreen
        }"
        id="pdf-reader-container">

        <!-- Header do Modal -->
        <header class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-green-500/10 to-teal-500/10 dark:from-green-500/20 dark:to-teal-500/20 flex-shrink-0">
            <!-- Informações do Livro -->
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="w-10 h-14 bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden flex-shrink-0 shadow-md">
                    <template x-if="selectedBook && selectedBook.cover_path">
                        <img :src="'/storage/' + selectedBook.cover_path" :alt="selectedBook.title" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!selectedBook || !selectedBook.cover_path">
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                    </template>
                </div>
                <div class="min-w-0">
                    <h3 x-text="selectedBook ? selectedBook.title : 'Carregando...'" class="text-base font-bold text-gray-900 dark:text-white truncate"></h3>
                    <p x-text="selectedBook ? selectedBook.author : ''" class="text-sm text-gray-500 dark:text-gray-400 truncate"></p>
                </div>
            </div>

            <!-- Controles -->
            <div class="flex items-center gap-2 ml-4">
                <!-- Modo de Visualização -->
                <div class="hidden sm:flex items-center gap-1 bg-gray-100 dark:bg-gray-700 rounded-full px-2 py-1">
                    <button @click="viewMode = 'single'" 
                        :class="viewMode === 'single' ? 'bg-gradient-to-r from-green-500 to-teal-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                        class="p-1.5 rounded-full transition-colors" title="Uma página">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </button>
                    <button @click="viewMode = 'double'" 
                        :class="viewMode === 'double' ? 'bg-gradient-to-r from-green-500 to-teal-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                        class="p-1.5 rounded-full transition-colors" title="Duas páginas">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </button>
                    <button @click="viewMode = 'scroll'" 
                        :class="viewMode === 'scroll' ? 'bg-gradient-to-r from-green-500 to-teal-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                        class="p-1.5 rounded-full transition-colors" title="Livro inteiro (scroll)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </button>
                </div>

                <!-- Navegação de Páginas -->
                <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 rounded-full px-2 py-1" x-show="viewMode !== 'scroll'">
                    <button @click="goToPrevPage()" :disabled="pageNum <= 1" 
                        class="p-1.5 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    
                    <div class="flex items-center gap-1 px-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        <input type="number" x-model.number="pageNum" @change="goToPage(pageNum)" 
                            :max="totalPages || 1" min="1"
                            class="w-12 text-center bg-transparent border-none focus:ring-0 font-bold text-gray-900 dark:text-white p-0">
                        <span class="text-gray-400">/</span>
                        <span x-text="totalPages || '...'"></span>
                    </div>
                    
                    <button @click="goToNextPage()" :disabled="pageNum >= totalPages" 
                        class="p-1.5 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>

                <!-- Página atual no modo scroll -->
                <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 rounded-full px-3 py-1.5" x-show="viewMode === 'scroll'">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Página <span x-text="pageNum" class="font-bold text-gray-900 dark:text-white"></span> / <span x-text="totalPages || '...'"></span>
                    </span>
                </div>

                <!-- Zoom -->
                <div class="hidden sm:flex items-center gap-1 bg-gray-100 dark:bg-gray-700 rounded-full px-2 py-1">
                    <button @click="zoomOut()" :disabled="pdfScale <= 0.5" 
                        class="p-1.5 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 disabled:opacity-40 transition-colors">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                    </button>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 px-1 min-w-[3rem] text-center" x-text="Math.round(pdfScale * 100) + '%'"></span>
                    <button @click="zoomIn()" :disabled="pdfScale >= 3" 
                        class="p-1.5 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 disabled:opacity-40 transition-colors">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                </div>

                <!-- Botões de Ação -->
                <div class="flex items-center gap-1">
                    <!-- Download -->
                    <a :href="selectedBook ? '/storage/' + selectedBook.file_path : '#'" 
                       :download="selectedBook ? selectedBook.title + '.pdf' : ''"
                       class="p-2 rounded-full bg-blue-500/10 hover:bg-blue-500/20 text-blue-500 transition-colors" 
                       title="Baixar PDF">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    </a>

                    <!-- Tela Inteira -->
                    <button @click="toggleFullscreen()" 
                        class="p-2 rounded-full bg-green-500/10 hover:bg-green-500/20 text-green-500 transition-colors" 
                        :title="isFullscreen ? 'Sair da tela inteira' : 'Tela inteira'">
                        <!-- Ícone 4 setas para expandir -->
                        <svg x-show="!isFullscreen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                        <!-- Ícone 2 setas opostas para minimizar -->
                        <svg x-show="isFullscreen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25"></path></svg>
                    </button>

                    <!-- Fechar -->
                    <button @click="closeReader()" class="p-2 rounded-full bg-red-500/10 hover:bg-red-500/20 text-red-500 transition-colors" title="Fechar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Corpo do Modal (Canvas do PDF) -->
        <div class="flex-1 overflow-auto bg-gray-200 dark:bg-gray-900 relative" id="pdf-container"
             @scroll="handlePdfScroll($event)">
            <!-- Loader -->
            <div x-show="loading" class="absolute inset-0 flex flex-col items-center justify-center bg-white/80 dark:bg-gray-900/80 z-10">
                <svg class="animate-spin h-10 w-10 text-teal-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Carregando página...</p>
            </div>
            
            <!-- Erro -->
            <div x-show="pdfError" class="absolute inset-0 flex flex-col items-center justify-center bg-white dark:bg-gray-900 z-10">
                <svg class="w-16 h-16 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Erro ao carregar PDF</h4>
                <p class="text-gray-500 dark:text-gray-400 text-sm text-center max-w-md" x-text="pdfError"></p>
                <button @click="retryLoadPdf()" class="mt-4 px-4 py-2 bg-gradient-to-r from-green-500 to-teal-600 text-white rounded-lg hover:shadow-lg transition-all">
                    Tentar novamente
                </button>
            </div>
            
            <!-- Container das Páginas (modo single/double) -->
            <div x-show="viewMode !== 'scroll'" class="min-h-full flex items-start justify-center p-6">
                <div class="flex gap-4">
                    <canvas id="pdf-canvas" class="shadow-2xl rounded-lg bg-white max-w-full"></canvas>
                    <canvas id="pdf-canvas-2" x-show="viewMode === 'double'" class="shadow-2xl rounded-lg bg-white max-w-full"></canvas>
                </div>
            </div>

            <!-- Container do Scroll Contínuo -->
            <div x-show="viewMode === 'scroll'" class="flex flex-col items-center gap-4 p-6" id="pdf-scroll-container">
                <!-- As páginas serão renderizadas aqui dinamicamente -->
            </div>
        </div>

        <!-- Footer com Progresso -->
        <footer class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex-shrink-0">
            <div class="flex items-center gap-4">
                <!-- Barra de Progresso Visual -->
                <div class="flex-1">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Progresso de Leitura</span>
                        <span class="text-xs font-bold text-teal-600 dark:text-teal-400" 
                              x-text="totalPages ? Math.round((pageNum / totalPages) * 100) + '%' : '0%'"></span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-teal-500 h-2 rounded-full transition-all duration-300"
                             :style="'width: ' + (totalPages ? (pageNum / totalPages) * 100 : 0) + '%'"></div>
                    </div>
                </div>
                
                <!-- Status de Salvamento -->
                <div class="flex items-center gap-2 text-xs" x-show="progressSaved">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-green-600 dark:text-green-400 font-medium">Progresso salvo</span>
                </div>
            </div>
        </footer>
    </div>
</div>
