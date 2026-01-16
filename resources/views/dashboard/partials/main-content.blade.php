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
                               class="w-full lg:w-80 pl-10 pr-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm placeholder-gray-400">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </form>
                
                <button @click="showCreate = true" 
                   class="flex items-center gap-2 bg-indigo-600 text-white px-4 py-2.5 rounded-lg font-medium text-sm hover:bg-indigo-700 transition shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="hidden sm:inline">Enviar</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Alerta de Sucesso -->
    @if (session('status') === 'livro-enviado')
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium">Sucesso!</span>
            <span class="text-sm">Livro enviado para análise.</span>
        </div>
    </div>
    @endif

    <!-- Abas de Navegação -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-1">
        <div class="flex space-x-1 mb-6">
            <button @click="activeTab = 'todos'" 
                    :class="activeTab === 'todos' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                    class="px-4 py-2 rounded-lg font-medium transition-colors">
                Todos
            </button>
            <button @click="activeTab = 'novos'" 
                    :class="activeTab === 'novos' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                    class="px-4 py-2 rounded-lg font-medium transition-colors relative">
                Novos
                @if($newBooks->count() > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                        {{ $newBooks->count() }}
                    </span>
                @endif
            </button>
        </div>
    </div>

    <!-- Grade de Livros -->
    <div x-show="activeTab === 'todos'">
        @if($books->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nenhum livro encontrado</h3>
            <p class="text-gray-500 dark:text-gray-400">Tente ajustar sua busca ou envie um novo livro.</p>
        </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @foreach($books as $book)
                    <div class="group cursor-pointer transform transition-all duration-300 hover:scale-105"
                         @click="openReader({{ $book }})">
                        
                        <div class="relative w-full aspect-[3/4] bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-200 dark:border-gray-700">
                            @if($book->cover_path)
                                <img src="{{ asset('storage/' . $book->cover_path) }}" 
                                     alt="{{ $book->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 p-4">
                                    <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <span class="text-xs text-center">Sem capa</span>
                                </div>
                            @endif
                            
                            <!-- Overlay de informações -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="absolute bottom-0 left-0 right-0 p-3 text-white">
                                    <h4 class="font-semibold text-sm line-clamp-2 mb-1">{{ $book->title }}</h4>
                                    <p class="text-xs opacity-90">{{ $book->author }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-2 px-1">
                            <h4 class="font-medium text-sm text-gray-900 dark:text-white line-clamp-1">{{ $book->title }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $book->author }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Livros Novos -->
    <div x-show="activeTab === 'novos'" x-transition>
        @if($newBooks->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nenhum livro novo esta semana</h3>
            <p class="text-gray-500 dark:text-gray-400">Volte em breve para ver as novidades!</p>
        </div>
        @else
            <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        {{ $newBooks->count() }} livro(s) novo(s) nos últimos 7 dias
                    </span>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @foreach($newBooks as $book)
                    <div class="group cursor-pointer transform transition-all duration-300 hover:scale-105 relative"
                         @click="openReader({{ $book }})">
                        
                        <!-- Badge de Novo -->
                        <div class="absolute top-2 right-2 z-10 bg-red-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                            Novo
                        </div>
                        
                        <div class="relative w-full aspect-[3/4] bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-200 dark:border-gray-700">
                            @if($book->cover_path)
                                <img src="{{ asset('storage/' . $book->cover_path) }}" 
                                     alt="{{ $book->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 p-4">
                                    <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <span class="text-xs text-center">Sem capa</span>
                                </div>
                            @endif
                            
                            <!-- Overlay de informações -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="absolute bottom-0 left-0 right-0 p-3 text-white">
                                    <h4 class="font-semibold text-sm line-clamp-2 mb-1">{{ $book->title }}</h4>
                                    <p class="text-xs opacity-90">{{ $book->author }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-2 px-1">
                            <h4 class="font-medium text-sm text-gray-900 dark:text-white line-clamp-1">{{ $book->title }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $book->author }}</p>
                            <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                {{ $book->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @include('dashboard.partials.reading-progress')
</div>
