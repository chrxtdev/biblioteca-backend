<!-- Conteúdo Principal -->
<div class="p-6 space-y-6">
    <!-- Header com Busca -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Olá, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Explore nosso acervo de livros</p>
            </div>

            <div class="flex gap-3 w-full lg:w-auto">
                <form method="GET" action="{{ route('aluno') }}" class="flex-1 lg:flex-initial">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Buscar livros, autores..."
                               class="w-full lg:w-80 pl-10 pr-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm placeholder-gray-400">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </form>

                <button @click="showCreate = true"
                   class="flex items-center gap-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white px-5 py-2.5 rounded-full font-semibold text-sm shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span class="hidden sm:inline">Enviar Livro</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Alerta de Sucesso -->
    @if (session('status') === 'livro-enviado')
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-medium">Sucesso!</span>
            <span class="text-sm">Livro enviado para análise.</span>
        </div>
    </div>
    @endif

    <!-- Abas de Navegação -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-2">
        <div class="flex space-x-2">
            <button @click="activeTab = 'todos'"
                    :class="{ 'bg-gradient-to-r from-green-500 to-teal-600 text-white shadow-md': activeTab === 'todos', 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600': activeTab !== 'todos' }"
                    class="flex-1 px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-300">
                Todos os Livros
            </button>
            <button @click="activeTab = 'novos'"
                    :class="{ 'bg-gradient-to-r from-green-500 to-teal-600 text-white shadow-md': activeTab === 'novos', 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600': activeTab !== 'novos' }"
                    class="flex-1 px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-300 relative">
                Novidades
                @if($newBooks->count() > 0)
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center border-2 border-white dark:border-gray-800">
                        {{ $newBooks->count() }}
                    </span>
                @endif
            </button>
        </div>
    </div>

    <!-- Grade de Livros -->
    <div x-show="activeTab === 'todos'" x-cloak>
        @if($books->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nenhum livro encontrado</h3>
                <p class="text-gray-500 dark:text-gray-400">Nenhum livro corresponde à sua busca ou filtro.</p>
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

    <!-- Livros Novos -->
    <div x-show="activeTab === 'novos'" x-transition x-cloak>
        @if($newBooks->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nenhum livro novo esta semana</h3>
                <p class="text-gray-500 dark:text-gray-400">Volte em breve para ver as novidades!</p>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @foreach($newBooks as $book)
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
