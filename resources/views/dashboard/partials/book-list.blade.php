<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

        {{-- BUSCA E HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-end gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Olá, {{ Auth::user()->name }} 👋</h2>
                <p class="text-gray-500 dark:text-gray-400">Explore o conhecimento disponível.</p>
            </div>
            <form method="GET" action="{{ route('dashboard') }}" class="w-full md:w-1/3 relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Buscar livros, autores..." 
                       class="w-full pl-10 pr-4 py-3 border-none rounded-xl shadow-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-sm placeholder-gray-400">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>
        </div>

        {{-- ALERTA DE SUCESSO --}}
        @if (session('status') === 'livro-enviado')
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Sucesso!</strong>
            <span class="block sm:inline">Livro enviado para análise.</span>
        </div>
        @endif

        {{-- MEUS ENVIOS --}}
        @if(!$myBooks->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-300">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    Seus Envios
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-medium">
                        <tr>
                            <th class="px-6 py-3">Título</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($myBooks as $myBook)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-gray-800 dark:text-gray-300">
                            <td class="px-6 py-3 font-medium">{{ $myBook->title }}</td>
                            <td class="px-6 py-3">
                                @if($myBook->is_verified)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Aprovado</span>
                                @elseif($myBook->rejection_reason)
                                    <div class="group relative inline-block">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 cursor-help">Recusado (?)</span>
                                        <div class="hidden group-hover:block absolute z-50 bottom-full left-0 mb-2 w-48 bg-gray-800 text-white text-xs rounded p-2 shadow-lg">
                                            {{ $myBook->rejection_reason }}
                                        </div>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">Em Análise</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $myBook->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- VITRINE --}}
        <div class="space-y-12">
            @if($books->isEmpty())
                <div class="text-center py-20">
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 transition-colors duration-300">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Nenhum livro encontrado</h3>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Tente buscar por outro termo.</p>
                </div>
            @else
                @foreach($books->groupBy('course') as $course => $courseBooks)
                <div class="relative group/section">
                    <div class="flex items-center gap-3 mb-4">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $course }}</h3>
                        <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200 text-xs px-2 py-1 rounded-md font-bold">{{ $courseBooks->count() }}</span>
                    </div>
                    
                    <div class="flex overflow-x-auto pb-8 pt-2 gap-6 scrollbar-hide snap-x">
                        @foreach($courseBooks as $book)
                            <div class="snap-start flex-none w-[160px] md:w-[180px] group cursor-pointer"
                                 @click="openReader('{{ asset('storage/' . $book->file_path) }}', '{{ $book->title }}')">
                                
                                <div class="relative w-full aspect-[2/3] bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform group-hover:-translate-y-2 overflow-hidden border border-gray-200 dark:border-gray-700">
                                    @if($book->cover_path)
                                        <img src="{{ asset('storage/' . $book->cover_path) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 p-4 text-center">
                                            <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            <span class="text-xs font-bold uppercase tracking-wider">{{ Str::limit($book->title, 30) }}</span>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center">
                                        <div class="transform scale-0 group-hover:scale-100 transition-all duration-300 bg-white text-indigo-600 rounded-full p-3 shadow-lg">
                                            <svg class="w-6 h-6 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <h4 class="font-bold text-gray-900 dark:text-gray-100 text-sm leading-tight truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">{{ $book->title }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-1">{{ $book->author }}</p>
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