<!-- Sidebar (Reescrita do Zero) -->
<div :class="sidebarOpen ? 'w-64' : 'w-20'" class="bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-screen sticky top-0 transition-all duration-300 ease-in-out z-30">

    <!-- 1. Seção do Logo (Topo) -->
    <div class="flex items-center justify-center pt-6 pb-4 flex-shrink-0">
        <a href="{{ route('aluno') }}" class="transition-all duration-300" :class="sidebarOpen ? 'h-12' : 'h-10'">
            <img src="{{ asset('images/unicentroma-logo.png') }}" alt="Unicentro" class="h-full w-auto object-contain" x-cloak>
        </a>
    </div>

    <!-- 2. Seção de Navegação (Meio, Expansível) -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-4">
        <ul class="space-y-2 px-4">
            <li>
                <a href="{{ route('aluno') }}" title="Início" class="flex items-center gap-3 py-2.5 px-3 rounded-lg transition-all duration-200 {{ request()->routeIs('aluno') ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}" :class="{'justify-center': !sidebarOpen}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="font-medium whitespace-nowrap" x-show="sidebarOpen" x-cloak>Início</span>
                </a>
            </li>
            <li>
                <a href="{{ route('favorites.index') }}" title="Favoritos" class="flex items-center gap-3 py-2.5 px-3 rounded-lg transition-all duration-200 {{ request()->routeIs('favorites.index') ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}" :class="{'justify-center': !sidebarOpen}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    <span class="font-medium whitespace-nowrap" x-show="sidebarOpen" x-cloak>Favoritos</span>
                </a>
            </li>
            <li>
                <button @click.prevent="showSubmissions = true" title="Meus Envios" class="w-full flex items-center gap-3 py-2.5 px-3 rounded-lg transition-all duration-200 text-left text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'justify-center': !sidebarOpen}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    <span class="font-medium whitespace-nowrap" x-show="sidebarOpen" x-cloak>Meus envios</span>
                </button>
            </li>
        </ul>
    </nav>

    <!-- 3. Seção de Ações (Rodapé) -->
    <div class="flex-shrink-0 p-4 space-y-4 border-t border-gray-200 dark:border-gray-700">
        <a href="{{ route('profile.edit') }}" title="Perfil" class="flex items-center gap-3" :class="{'justify-center': !sidebarOpen}">
            <img class="w-9 h-9 rounded-full object-cover flex-shrink-0" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random&color=fff" alt="Avatar">
            <div class="flex-1" x-show="sidebarOpen" x-cloak>
                <p class="font-semibold text-sm text-gray-800 dark:text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Ver Perfil</p>
            </div>
        </a>
        <div class="flex items-center gap-2">
            <button @click="toggleTheme()" title="Mudar Tema" class="flex-1 flex items-center justify-center text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 p-2 rounded-lg bg-gray-100 dark:bg-gray-700/50 transition-colors">
                <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </button>
            <button @click="sidebarOpen = !sidebarOpen" title="Recolher/Expandir" class="flex-1 flex items-center justify-center text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 p-2 rounded-lg bg-gray-100 dark:bg-gray-700/50 transition-colors">
                <svg x-show="sidebarOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                <svg x-show="!sidebarOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
            </button>
            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                @csrf
                <button type="submit" title="Sair" class="w-full flex items-center justify-center text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-500 p-2 rounded-lg bg-gray-100 dark:bg-gray-700/50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H9m6 0v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v6z"></path></svg>
                </button>
            </form>
        </div>
    </div>
</div>
