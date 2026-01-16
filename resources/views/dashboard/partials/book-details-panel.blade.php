<!-- Book Details Panel (Coluna Direita) -->
<div class="w-96 bg-gradient-to-b from-indigo-600 to-indigo-800 p-6 overflow-y-auto">
    <!-- Book Details -->
    <div x-show="selectedBook" x-cloak>
        <div class="text-white">
            <!-- Book Cover -->
            <div class="mb-6">
                <img :src="selectedBook.cover || '/placeholder-book.jpg'" 
                     :alt="selectedBook.title" 
                     class="w-full h-64 object-cover rounded-lg shadow-xl">
            </div>

            <!-- Book Info -->
            <h3 class="text-2xl font-bold mb-2" x-text="selectedBook.title"></h3>
            <p class="text-indigo-200 mb-4" x-text="selectedBook.author"></p>

            <!-- Rating -->
            <div class="flex items-center gap-2 mb-6">
                <div class="flex text-yellow-400">
                    <template x-for="i in 5">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.005 1.347-1.24.588-1.81l-2.8-2.034a1 1 0 00-.364-1.118L9.049 2.927z"></path>
                        </svg>
                    </template>
                </div>
                <span class="text-white font-semibold" x-text="selectedBook.rating || '4.8'"></span>
            </div>

            <!-- Stats -->
            <div class="space-y-3 mb-6">
                <div class="flex justify-between">
                    <span class="text-indigo-200">Pages</span>
                    <span class="text-white font-semibold" x-text="selectedBook.pages || '320'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-indigo-200">Ratings</span>
                    <span class="text-white font-semibold" x-text="selectedBook.ratings || '643'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-indigo-200">Reviews</span>
                    <span class="text-white font-semibold" x-text="selectedBook.reviews || '110'"></span>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <h4 class="font-semibold mb-2">Description</h4>
                <p class="text-indigo-100 text-sm leading-relaxed" x-text="selectedBook.description || 'A comprehensive guide to modern business practices and strategies.'"></p>
            </div>

            <!-- Read Button -->
            <button @click="openReader(selectedBook.file, selectedBook.title, selectedBook.id)"
                    class="w-full bg-white text-indigo-600 py-3 rounded-lg font-semibold hover:bg-indigo-50 transition-colors flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                Read Now
            </button>
        </div>
    </div>

    <!-- Default State -->
    <div x-show="!selectedBook" class="text-white text-center py-12">
        <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
        </div>
        <h3 class="text-xl font-semibold mb-2">Select a Book</h3>
        <p class="text-indigo-200">Choose a book from the library to view details</p>
    </div>
</div>
