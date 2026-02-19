<!-- Painel de Estatísticas (Coluna Direita) -->
<div class="w-full bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 space-y-5">

    @if(!request()->routeIs('favorites.index'))
    <!-- Estatísticas (Redesigned) -->
    <div>
        <h3 class="flex items-center gap-2 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            Visão Geral
        </h3>
        
        <div class="grid grid-cols-1 gap-3">
            <!-- Total -->
            <div class="relative overflow-hidden group bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30 transition-all duration-300">
                <div class="absolute right-0 top-0 p-3 opacity-10 transform translate-x-2 -translate-y-2 group-hover:scale-110 transition-transform">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div class="relative z-10">
                    <p class="text-blue-100 text-xs font-semibold uppercase tracking-wide">Acervo Total</p>
                    <p class="text-white text-3xl font-bold mt-1">{{ $totalBooksCount ?? $books->total() }}</p>
                    <p class="text-blue-200 text-xs mt-1">livros disponíveis</p>
                </div>
            </div>

            <!-- Novos & Envios (Grid Split) -->
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white dark:bg-gray-700/50 p-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-green-500/50 transition-colors group">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        </div>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $newBooks->count() }}</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Novidades (7d)</p>
                </div>

                <div class="bg-white dark:bg-gray-700/50 p-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-purple-500/50 transition-colors group">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        </div>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $myBooks->count() }}</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Meus Envios</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Categorias (Redesigned) -->
    <div>
        <h3 class="flex items-center gap-2 text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4 mt-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            Explorar
        </h3>
        
        <div class="space-y-1">
             {{-- Link para Todas --}}
             <a href="{{ request()->routeIs('favorites.index') ? route('favorites.index') : route('aluno') }}" 
               class="group flex items-center justify-between p-2.5 rounded-xl transition-all duration-200 {{ !request('course') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-semibold' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-600 dark:text-gray-400' }}">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ !request('course') ? 'bg-white dark:bg-gray-600 shadow-sm' : 'bg-gray-100 dark:bg-gray-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </div>
                    <span>Todas as Categorias</span>
                </div>
            </a>

            @php
                // Definição de Cores/Icones para as Categorias
                $catStyles = [
                    'Engenharia Civil' => ['color' => 'blue', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                    'Direito' => ['color' => 'green', 'icon' => 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3'],
                    'Administração' => ['color' => 'purple', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    'Psicologia' => ['color' => 'yellow', 'icon' => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'Serviço Social' => ['color' => 'pink', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                    'Fisioterapia' => ['color' => 'teal', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                    'Enfermagem' => ['color' => 'sky', 'icon' => 'M19 14l-7 7m0 0l-7-7m7 7V3'],
                    'Conteúdos Gerais' => ['color' => 'gray', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                ];
            @endphp

            @foreach(\App\Services\BookService::getMainCourses() as $courseName)
                @php 
                    $style = $catStyles[$courseName] ?? ['color' => 'gray', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'];
                    $color = $style['color'];
                    $isActive = request('course') === $courseName;
                @endphp
                <a href="{{ request()->routeIs('favorites.index') ? route('favorites.index', ['course' => $courseName]) : route('aluno', ['course' => $courseName]) }}" 
                   class="group flex items-center justify-between p-2.5 rounded-xl transition-all duration-200 {{ $isActive ? 'bg-'.$color.'-50 dark:bg-'.$color.'-900/20 text-'.$color.'-700 dark:text-'.$color.'-300' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-600 dark:text-gray-400' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ $isActive ? 'bg-white dark:bg-'.$color.'-800 shadow-sm' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-'.$color.'-100 dark:group-hover:bg-'.$color.'-900/40 group-hover:text-'.$color.'-600' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $style['icon'] }}"></path></svg>
                        </div>
                        <span class="font-medium text-sm">{{ $courseName }}</span>
                    </div>
                    @if($isActive)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
