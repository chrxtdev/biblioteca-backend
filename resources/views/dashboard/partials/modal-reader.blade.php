<!-- Modal Leitor de PDF -->
<div x-show="showReader" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>

    <div @click.outside="showReader = false; pdfDoc = null;"
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl h-[95vh] flex flex-col">

        <!-- Header do Modal -->
        <header class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex-1 min-w-0">
                <h3 x-text="selectedBook ? selectedBook.title : 'Carregando...'" class="text-lg font-bold text-gray-900 dark:text-white truncate"></h3>
                <p x-text="selectedBook ? selectedBook.author : ''" class="text-sm text-gray-500 dark:text-gray-400 truncate"></p>
            </div>
            <div class="flex items-center gap-2 ml-4">
                <!-- Controles de Navegação -->
                <button @click="goToPrevPage()" :disabled="pageNum <= 1" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    <span x-text="pageNum"></span> / <span x-text="pdfDoc ? pdfDoc.numPages : '...' "></span>
                </div>
                <button @click="goToNextPage()" :disabled="pageNum >= (pdfDoc ? pdfDoc.numPages : 0)" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
                <button @click="showReader = false; pdfDoc = null;" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 ml-4">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </header>

        <!-- Corpo do Modal (Canvas do PDF) -->
        <div class="flex-1 overflow-auto p-4 bg-gray-100 dark:bg-gray-900/50 relative">
            <div x-show="loading" class="absolute inset-0 flex items-center justify-center">
                <svg class="animate-spin h-8 w-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <canvas id="pdf-canvas" x-show="!loading"></canvas>
        </div>
    </div>
</div>
