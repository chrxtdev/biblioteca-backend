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
                               class="w-full lg:w-80 pl-10 pr-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent text-sm placeholder-gray-400">
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

    <!-- Abas de Navegação + Seletor de Quantidade -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex space-x-2 flex-1">
                <button @click="activeTab = 'todos'"
                        :class="{ 'bg-gradient-to-r from-green-500 to-teal-600 text-white shadow-md': activeTab === 'todos', 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600': activeTab !== 'todos' }"
                        class="flex-1 px-4 py-2 rounded-lg font-semibold text-sm"
                        :style="isLoaded ? 'transition: all 300ms ease-in-out' : ''">
                    Todos os Livros
                </button>
                <button @click="activeTab = 'novos'; markNewsSeen()"
                        :class="{ 'bg-gradient-to-r from-green-500 to-teal-600 text-white shadow-md': activeTab === 'novos', 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600': activeTab !== 'novos' }"
                        class="flex-1 px-4 py-2 rounded-lg font-semibold text-sm relative"
                        :style="isLoaded ? 'transition: all 300ms ease-in-out' : ''">
                    Novidades
                    @if($newBooks->count() > 0)
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center border-2 border-white dark:border-gray-800"
                              x-show="!newsSeen"
                              x-transition:leave="transition ease-in duration-300"
                              x-transition:leave-start="opacity-100 scale-100"
                              x-transition:leave-end="opacity-0 scale-90">
                            {{ $newBooks->count() }}
                        </span>
                    @endif
                </button>
            </div>

            <!-- Seletor de Quantidade por Página -->
            <div class="flex items-center gap-2" x-show="activeTab === 'todos'">
                <span class="text-sm text-gray-500 dark:text-gray-400">Exibir:</span>
                <form method="GET" action="{{ route('aluno') }}" id="perPageForm">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('course'))
                        <input type="hidden" name="course" value="{{ request('course') }}">
                    @endif
                    <select name="per_page" onchange="document.getElementById('perPageForm').submit()"
                            class="text-sm bg-gray-100 dark:bg-gray-700 border-0 rounded-lg py-2 pl-3 pr-8 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-teal-500 cursor-pointer appearance-none"
                            style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%236b7280%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1rem;">
                        <option value="12" {{ request('per_page', 24) == 12 ? 'selected' : '' }}>12</option>
                        <option value="24" {{ request('per_page', 24) == 24 ? 'selected' : '' }}>24</option>
                        <option value="48" {{ request('per_page', 24) == 48 ? 'selected' : '' }}>48</option>
                        <option value="96" {{ request('per_page', 24) == 96 ? 'selected' : '' }}>96</option>
                    </select>
                </form>
                <span class="text-sm text-gray-500 dark:text-gray-400">por página</span>
            </div>
        </div>
    </div>

    <!-- Aba: Todos os Livros -->
    <div x-show="activeTab === 'todos'" x-cloak>
        @if($books->isEmpty() && !$course && !$search)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nenhum livro encontrado</h3>
                <p class="text-gray-500 dark:text-gray-400">O acervo ainda está vazio.</p>
            </div>
        @elseif($course || $search)
            {{-- Modo filtrado: mostrar grade simples --}}
            @if($books->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nenhum livro encontrado</h3>
                    <p class="text-gray-500 dark:text-gray-400">Nenhum livro corresponde à sua busca ou filtro.</p>
                    <a href="{{ route('aluno') }}" class="mt-4 inline-flex items-center gap-2 text-teal-600 hover:text-teal-700 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Voltar para todos os livros
                    </a>
                </div>
            @else
                {{-- Cabeçalho do filtro ativo --}}
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            @if($course)
                                📚 {{ $course }}
                            @elseif($search)
                                🔍 Resultados para "{{ $search }}"
                            @endif
                        </h2>
                        <span class="px-3 py-1 bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 rounded-full text-sm font-medium">
                            {{ $books->total() }} livros
                        </span>
                    </div>
                    <a href="{{ route('aluno') }}" class="flex items-center gap-2 text-gray-500 hover:text-teal-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Limpar filtro
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    @foreach($books as $book)
                        @include('dashboard.partials.book-card', [
                            'book' => $book,
                            'readingProgress' => $readingProgress,
                            'favoriteBookIds' => $favoriteBookIds
                        ])
                    @endforeach
                </div>

                {{-- Paginação --}}
                @if($books->hasPages())
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Mostrando <span class="font-semibold text-gray-900 dark:text-white">{{ $books->firstItem() }}</span> 
                        a <span class="font-semibold text-gray-900 dark:text-white">{{ $books->lastItem() }}</span> 
                        de <span class="font-semibold text-gray-900 dark:text-white">{{ $books->total() }}</span> livros
                    </div>
                    
                    <div class="flex items-center gap-2">
                        @if($books->onFirstPage())
                            <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-lg cursor-not-allowed">← Anterior</span>
                        @else
                            <a href="{{ $books->previousPageUrl() }}" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-teal-600 rounded-lg hover:shadow-lg transition-all duration-300">← Anterior</a>
                        @endif

                        <div class="hidden sm:flex items-center gap-1">
                            @foreach($books->getUrlRange(max(1, $books->currentPage() - 2), min($books->lastPage(), $books->currentPage() + 2)) as $page => $url)
                                @if($page == $books->currentPage())
                                    <span class="px-3 py-2 text-sm font-bold text-white bg-gradient-to-r from-green-500 to-teal-600 rounded-lg">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">{{ $page }}</a>
                                @endif
                            @endforeach
                        </div>

                        @if($books->hasMorePages())
                            <a href="{{ $books->nextPageUrl() }}" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-teal-600 rounded-lg hover:shadow-lg transition-all duration-300">Próximo →</a>
                        @else
                            <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-lg cursor-not-allowed">Próximo →</span>
                        @endif
                    </div>
                </div>
                @endif
            @endif
        @else
            {{-- Modo Vitrine: organizado por curso --}}
            @php
                // Cores mapeadas com classes completas para o Tailwind compilar
                $courseStyles = [
                    'Engenharia Civil' => [
                        'dot' => 'bg-blue-500',
                        'badge' => 'bg-blue-100 text-blue-700 dark:bg-blue-800 dark:text-blue-200',
                        'link' => 'text-blue-600 dark:text-blue-400 hover:text-blue-700'
                    ],
                    'Direito' => [
                        'dot' => 'bg-green-500',
                        'badge' => 'bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200',
                        'link' => 'text-green-600 dark:text-green-400 hover:text-green-700'
                    ],
                    'Administração' => [
                        'dot' => 'bg-purple-500',
                        'badge' => 'bg-purple-100 text-purple-700 dark:bg-purple-800 dark:text-purple-200',
                        'link' => 'text-purple-600 dark:text-purple-400 hover:text-purple-700'
                    ],
                    'Psicologia' => [
                        'dot' => 'bg-yellow-500',
                        'badge' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-800 dark:text-yellow-200',
                        'link' => 'text-yellow-600 dark:text-yellow-400 hover:text-yellow-700'
                    ],
                    'Serviço Social' => [
                        'dot' => 'bg-pink-500',
                        'badge' => 'bg-pink-100 text-pink-700 dark:bg-pink-800 dark:text-pink-200',
                        'link' => 'text-pink-600 dark:text-pink-400 hover:text-pink-700'
                    ],
                    'Fisioterapia' => [
                        'dot' => 'bg-teal-500',
                        'badge' => 'bg-teal-100 text-teal-700 dark:bg-teal-800 dark:text-teal-200',
                        'link' => 'text-teal-600 dark:text-teal-400 hover:text-teal-700'
                    ],
                    'Enfermagem' => [
                        'dot' => 'bg-sky-500',
                        'badge' => 'bg-sky-100 text-sky-700 dark:bg-sky-800 dark:text-sky-200',
                        'link' => 'text-sky-600 dark:text-sky-400 hover:text-sky-700'
                    ],
                    'Geral' => [
                        'dot' => 'bg-gray-500',
                        'badge' => 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
                        'link' => 'text-gray-600 dark:text-gray-400 hover:text-gray-700'
                    ],
                ];
                $defaultStyle = [
                    'dot' => 'bg-gray-500',
                    'badge' => 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
                    'link' => 'text-gray-600 dark:text-gray-400 hover:text-gray-700'
                ];
            @endphp

            <div class="space-y-8">
                @foreach($booksByCourse as $courseName => $courseBooks)
                    @php $style = $courseStyles[$courseName] ?? $defaultStyle; @endphp
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                        {{-- Cabeçalho do Curso --}}
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full {{ $style['dot'] }}"></div>
                                <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ $courseName }}</h2>
                                <span class="px-2 py-0.5 {{ $style['badge'] }} rounded-full text-xs font-medium">
                                    {{ $courseBooks->count() }} {{ $courseBooks->count() === 1 ? 'livro' : 'livros' }}
                                </span>
                            </div>
                            <a href="{{ route('aluno', ['course' => $courseName]) }}" 
                               class="flex items-center gap-1 text-sm {{ $style['link'] }} font-medium transition-colors">
                                Ver todos
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>

                        {{-- Grid de Livros (máximo 5 por curso) --}}
                        <div class="p-6">
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                @foreach($courseBooks->take(5) as $book)
                                    @include('dashboard.partials.book-card', [
                                        'book' => $book,
                                        'readingProgress' => $readingProgress,
                                        'favoriteBookIds' => $favoriteBookIds
                                    ])
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Aba: Novidades -->
    <div x-show="activeTab === 'novos'" x-cloak>
        @if($newBooks->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Você está em dia! 🎉</h3>
                <p class="text-gray-500 dark:text-gray-400">Não há livros novos desde sua última visita.</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Volte em breve para conferir as novidades!</p>
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
