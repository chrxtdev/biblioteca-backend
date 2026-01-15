<x-app-layout>
    {{-- CSS "Gambiarra" para esconder a barra de navegação padrão do Breeze e deixar só a nossa --}}
    <style>
        nav[x-data] { display: none !important; }
    </style>

    <div x-data="{ 
            showReader: false, 
            pdfUrl: '', 
            bookTitle: '',
            openReader(url, title) {
                this.pdfUrl = url;
                this.bookTitle = title;
                this.showReader = true;
            }
        }">

        <x-slot name="header">
            <div class="flex justify-between items-center">
                {{-- LADO ESQUERDO: Logo e Título --}}
                <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-3">
                    {{-- Certifique-se que o arquivo existe em public/images/unicentroma-logo.png --}}
                    <img src="{{ asset('images/unicentroma-logo.png') }}" 
                         alt="Logo Faculdade" 
                         class="h-12 w-auto object-contain">
                    {{ __('Biblioteca Digital') }}
                </h2>
                
                {{-- LADO DIREITO: Botões de Ação --}}
                <div class="flex items-center">
                    <a href="{{ route('books.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Enviar Livro
                    </a>

                    {{-- Botão de Sair (Logout) --}}
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="ml-4 text-sm text-gray-500 hover:text-red-600 underline cursor-pointer">
                            Sair
                        </button>
                    </form>
                </div>
            </div>
        </x-slot>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

                {{-- 🔍 BARRA DE PESQUISA --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <form method="GET" action="{{ route('dashboard') }}" class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Pesquisar por título, autor ou curso..." 
                               class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-md text-sm hover:bg-gray-700">
                            Buscar
                        </button>
                        @if(request('search'))
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300 flex items-center">Limpar</a>
                        @endif
                    </form>
                </div>

                {{-- 📂 MEUS ENVIOS RECENTES --}}
                @if(!$myBooks->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-700">📂 Meus Envios Recentes</h3>
                        <span class="text-xs text-gray-400">Status dos seus uploads</span>
                    </div>
                    <div class="p-4 overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="text-gray-400 font-normal border-b">
                                <tr>
                                    <th class="pb-2">Livro</th>
                                    <th class="pb-2">Status</th>
                                    <th class="pb-2">Data</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600">
                                @foreach($myBooks as $myBook)
                                <tr class="border-b last:border-0 hover:bg-gray-50">
                                    <td class="py-3 font-medium text-gray-800">{{ $myBook->title }}</td>
                                    <td class="py-3">
                                        @if($myBook->is_verified)
                                            <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">Aprovado</span>
                                        @elseif($myBook->rejection_reason)
                                            <span class="bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full cursor-pointer" title="{{ $myBook->rejection_reason }}">Recusado</span>
                                        @else
                                            <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full">Análise</span>
                                        @endif
                                    </td>
                                    <td class="py-3">{{ $myBook->created_at->format('d/m') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-2">{{ $myBooks->links() }}</div>
                    </div>
                </div>
                @endif

                {{-- 🎬 CARROSSEL DE LIVROS (Estilo Netflix) --}}
                <div class="space-y-8">
                    @if($books->isEmpty())
                        <div class="text-center py-12 text-gray-500">
                            <p class="text-xl">Nenhum livro encontrado na biblioteca.</p>
                        </div>
                    @else
                        @foreach($books->groupBy('course') as $course => $courseBooks)
                        <div class="space-y-3">
                            <h3 class="text-xl font-bold text-gray-800 border-l-4 border-indigo-600 pl-3">
                                {{ $course }} <span class="text-gray-400 text-sm font-normal">({{ $courseBooks->count() }})</span>
                            </h3>
                            
                            <div class="flex overflow-x-auto pb-4 gap-4 scrollbar-hide" style="scrollbar-width: thin;">
                                @foreach($courseBooks as $book)
                                    <div class="flex-none w-40 group relative">
                                        {{-- Capa clicável --}}
                                        <div class="relative w-40 h-60 rounded-lg overflow-hidden shadow-md transition-transform transform group-hover:scale-105 group-hover:shadow-xl cursor-pointer"
                                             @click="openReader('{{ asset('storage/' . $book->file_path) }}', '{{ $book->title }}')">
                                            
                                            @if($book->cover_path)
                                                <img src="{{ asset('storage/' . $book->cover_path) }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-gray-200 flex flex-col items-center justify-center text-gray-400 p-2 text-center">
                                                    <span class="text-xs font-bold">{{ $book->title }}</span>
                                                    <svg class="w-8 h-8 mt-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                                </div>
                                            @endif

                                            {{-- Overlay "LER PDF" --}}
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 flex items-center justify-center transition-all">
                                                <span class="opacity-0 group-hover:opacity-100 bg-white text-gray-900 text-xs font-bold px-3 py-1 rounded-full shadow-lg transform translate-y-2 group-hover:translate-y-0 transition-all">
                                                    LER PDF
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Informações do Livro --}}
                                        <div class="mt-2">
                                            <h4 class="font-bold text-gray-900 text-sm truncate" title="{{ $book->title }}">{{ $book->title }}</h4>
                                            <p class="text-xs text-gray-500 truncate">{{ $book->author }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>

            </div>
        </div>

        {{--  MODAL DE LEITURA (POP-UP) --}}
        <div x-show="showReader" 
             style="display: none;"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90 backdrop-blur-sm p-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
             
            <div class="bg-white w-full max-w-6xl h-[90vh] rounded-lg shadow-2xl flex flex-col overflow-hidden relative" @click.away="showReader = false">
                
                {{-- Header do Modal --}}
                <div class="bg-gray-900 text-white p-4 flex justify-between items-center shadow-md z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center font-bold text-xs">PDF</div>
                        <div>
                            <h3 class="font-bold text-sm md:text-lg" x-text="bookTitle"></h3>
                            <p class="text-xs text-gray-400">Modo Leitura</p>
                        </div>
                    </div>
                    <button @click="showReader = false" class="text-gray-400 hover:text-white p-2 hover:bg-gray-800 rounded-full transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {{-- Iframe do PDF --}}
                <div class="flex-1 bg-gray-100 relative">
                    <iframe :src="pdfUrl" class="w-full h-full border-none"></iframe>
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none" x-show="!pdfUrl">
                        <p class="text-gray-500 animate-pulse">Carregando documento...</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>