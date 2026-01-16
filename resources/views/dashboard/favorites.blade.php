<!-- Conteúdo da Página de Favoritos -->
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Meus Livros Favoritos 📚</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Sua coleção de livros preferidos.</p>
    </div>

    <!-- Grade de Livros Favoritos -->
    <div>
        @if($books->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nenhum livro favorito</h3>
                <p class="text-gray-500 dark:text-gray-400">Clique no coração de um livro para adicioná-lo aqui.</p>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @foreach($books as $book)
                    @include('dashboard.partials.book-card', [
                        'book' => $book,
                        'readingProgress' => $readingProgress,
                        'favoriteBookIds' => $favoriteBookIds
                    ])
                @endforeach
            </div>
        @endif
    </div>
</div>
