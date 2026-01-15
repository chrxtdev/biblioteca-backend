<div x-show="showReader" style="display: none;"
     class="fixed inset-0 z-[60] flex items-center justify-center bg-black/95 backdrop-blur-sm p-0 md:p-4"
     x-transition.opacity>
    <div class="bg-gray-900 w-full h-full md:rounded-2xl shadow-2xl flex flex-col overflow-hidden relative" @click.away="showReader = false">
        <div class="bg-gray-800 px-4 py-3 flex justify-between items-center border-b border-gray-700">
            <div class="flex items-center gap-3">
                <div class="bg-indigo-500/20 text-indigo-300 p-2 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <h3 class="text-white font-medium truncate max-w-[200px] md:max-w-md" x-text="bookTitle"></h3>
            </div>
            <button @click="showReader = false" class="text-gray-400 hover:text-white bg-gray-700 hover:bg-gray-600 p-2 rounded-full transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="flex-1 bg-gray-900 relative">
            <iframe :src="pdfUrl" class="w-full h-full border-none"></iframe>
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none" x-show="!pdfUrl">
                <div class="text-center">
                    <div class="w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                    <p class="text-gray-400 text-sm">Carregando documento...</p>
                </div>
            </div>
        </div>
    </div>
</div>