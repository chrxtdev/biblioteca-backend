<!-- Conteúdo Principal -->
<div class="p-6 space-y-6">
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-gray-900 to-gray-800 rounded-3xl p-8 md:p-12 overflow-hidden shadow-2xl mb-10">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" />
            </svg>
        </div>
        
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="max-w-2xl">
                <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 tracking-tight">
                    Explore o Conhecimento
                </h1>
                <p class="text-lg text-gray-300 mb-8 leading-relaxed">
                    Bem-vindo ao seu acervo digital. Descubra livros, artigos e materiais acadêmicos selecionados para impulsionar seus estudos.
                </p>
                
                <!-- Search Bar in Hero -->
                <form method="GET" action="{{ route('aluno') }}" class="relative w-full max-w-lg">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Pesquisar por título, autor ou tópico..."
                               class="w-full pl-12 pr-4 py-4 rounded-xl border-0 bg-white/10 backdrop-blur-md text-white placeholder-gray-400 focus:ring-2 focus:ring-teal-400 focus:bg-white/20 transition-all shadow-lg">
                        <svg class="w-6 h-6 text-gray-400 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        @if(request('search'))
                            <a href="{{ route('aluno') }}" class="absolute right-4 top-4 text-gray-400 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
            
            <!-- Quick Actions -->
            <div class="flex flex-col gap-4">
                 <button @click="showCreate = true"
                    class="group flex items-center gap-3 bg-teal-500 hover:bg-teal-400 text-white px-6 py-4 rounded-xl font-bold shadow-lg hover:shadow-teal-500/30 transform hover:-translate-y-1 transition-all duration-300">
                    <div class="bg-white/20 p-2 rounded-lg group-hover:rotate-12 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <span>Enviar Livro</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Category Filters -->
    <div class="mb-8 overflow-x-auto pb-4 scrollbar-hide">
        <div class="flex gap-3">
            <a href="{{ route('aluno') }}" 
               class="whitespace-nowrap px-6 py-2.5 rounded-full font-medium {{ !request('course') && !request('search') ? 'bg-gray-900 text-white shadow-lg' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                Todas as Áreas
            </a>
            
            @foreach(\App\Enums\Course::values() as $courseName)
                <a href="{{ route('aluno', ['course' => $courseName]) }}"
                   class="whitespace-nowrap px-6 py-2.5 rounded-full font-medium {{ request('course') === $courseName ? 'bg-teal-500 text-white shadow-lg' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                   {{ $courseName }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Aba: Todos os Livros -->
    <div>
        @if($books->isEmpty() && !$course && !$search)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nenhum livro encontrado</h3>
                <p class="text-gray-500 dark:text-gray-400">O acervo ainda está vazio.</p>
            </div>
        @elseif($course || $search)
            {{-- Modo filtrado: mostrar grade simples --}}
            @if($books->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700/50 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nenhum livro encontrado</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">Não encontramos nada com esses filtros. Tente buscar por outros termos.</p>
                    <a href="{{ route('aluno') }}" class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Limpar todos os filtros
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
                    'Engenharias' => [
                        'dot' => 'bg-blue-500',
                        'badge' => 'bg-blue-100 text-blue-700 dark:bg-blue-800 dark:text-blue-200',
                        'link' => 'text-blue-600 dark:text-blue-400 hover:text-blue-700'
                    ],
                    'Ciências Humanas e Sociais' => [
                        'dot' => 'bg-purple-500',
                        'badge' => 'bg-purple-100 text-purple-700 dark:bg-purple-800 dark:text-purple-200',
                        'link' => 'text-purple-600 dark:text-purple-400 hover:text-purple-700'
                    ],
                    'Área da Saúde' => [
                        'dot' => 'bg-teal-500',
                        'badge' => 'bg-teal-100 text-teal-700 dark:bg-teal-800 dark:text-teal-200',
                        'link' => 'text-teal-600 dark:text-teal-400 hover:text-teal-700'
                    ],
                    'Tecnologia e TI' => [
                        'dot' => 'bg-cyan-500',
                        'badge' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-800 dark:text-cyan-200',
                        'link' => 'text-cyan-600 dark:text-cyan-400 hover:text-cyan-700'
                    ],
                    'Conteúdos Gerais' => [
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
    <div x-show="activeTab === 'novos'" x-cloak style="display: none;">
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
