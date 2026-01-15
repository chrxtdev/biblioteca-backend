<div x-show="showReader" style="display: none;"
     class="fixed inset-0 z-[60] flex items-center justify-center bg-black/95 backdrop-blur-sm p-0 md:p-4"
     x-transition.opacity>
    <div class="bg-gray-900 w-full h-full md:rounded-2xl shadow-2xl flex flex-col overflow-hidden relative" @click.away="showReader = false">
        <!-- Header com informações e progresso -->
        <div class="bg-gray-800 px-4 py-3 border-b border-gray-700">
            <div class="flex justify-between items-center mb-2">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-500/20 text-indigo-300 p-2 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-medium truncate max-w-[200px] md:max-w-md" x-text="bookTitle"></h3>
                        <div class="flex items-center gap-2 text-xs text-gray-400">
                            <span>Página <span x-text="currentPage"></span> de <span x-text="totalPages || '?'"></span></span>
                            <span x-show="readingProgress.progress_percentage" class="text-indigo-400">
                                (<span x-text="Math.round(readingProgress.progress_percentage || 0)"></span>%)
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Botão de concluir -->
                    <button x-show="!readingProgress.is_completed && totalPages > 0" 
                            @click="markAsCompleted()"
                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Concluir
                    </button>
                    
                    <!-- Badge de concluído -->
                    <div x-show="readingProgress.is_completed" 
                         class="bg-green-600 text-white px-3 py-1.5 rounded-lg text-sm font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Concluído
                    </div>
                    
                    <button @click="showReader = false" class="text-gray-400 hover:text-white bg-gray-700 hover:bg-gray-600 p-2 rounded-full transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
            
            <!-- Barra de progresso -->
            <div x-show="totalPages > 0" class="w-full bg-gray-700 rounded-full h-2">
                <div class="bg-indigo-500 h-2 rounded-full transition-all duration-300" 
                     :style="`width: ${Math.round((currentPage / totalPages) * 100)}%`"></div>
            </div>
        </div>
        
        <!-- Área do PDF -->
        <div class="flex-1 bg-gray-900 relative">
            <iframe :src="pdfUrl" 
                    @load="updateProgress(1, totalPages)"
                    class="w-full h-full border-none"
                    id="pdfViewer"></iframe>
            
            <!-- Loading -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none" x-show="!pdfUrl">
                <div class="text-center">
                    <div class="w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                    <p class="text-gray-400 text-sm">Carregando documento...</p>
                </div>
            </div>
            
            <!-- Controles de navegação -->
            <div x-show="totalPages > 0" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-gray-800/90 backdrop-blur-sm rounded-lg px-4 py-2 flex items-center gap-3">
                <button @click="navigatePage('prev')" 
                        :disabled="currentPage <= 1"
                        class="text-gray-400 hover:text-white disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                
                <div class="text-white text-sm font-medium">
                    <input type="number" 
                           x-model="currentPage" 
                           @change="updateProgress(currentPage, totalPages)"
                           min="1" 
                           :max="totalPages"
                           class="w-16 bg-gray-700 text-center rounded px-2 py-1 text-sm">
                    / <span x-text="totalPages"></span>
                </div>
                
                <button @click="navigatePage('next')" 
                        :disabled="currentPage >= totalPages"
                        class="text-gray-400 hover:text-white disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function navigatePage(direction) {
    const iframe = document.getElementById('pdfViewer');
    if (!iframe.contentWindow) return;
    
    try {
        if (direction === 'next') {
            iframe.contentWindow.postMessage({ type: 'nextPage' }, '*');
        } else {
            iframe.contentWindow.postMessage({ type: 'prevPage' }, '*');
        }
    } catch (error) {
        console.log('Erro ao navegar:', error);
    }
}

// Listener para atualizar página atual do PDF
window.addEventListener('message', function(event) {
    if (event.data.type === 'pageChange') {
        // Atualiza o Alpine.js com a página atual
        if (window.Alpine && window.Alpine.store) {
            window.Alpine.store('reader').currentPage = event.data.page;
            window.Alpine.store('reader').updateProgress(event.data.page, window.Alpine.store('reader').totalPages);
        }
    }
});
</script>