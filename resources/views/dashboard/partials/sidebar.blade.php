<!-- Sidebar -->
<div :class="sidebarOpen ? 'w-64' : 'w-20'" class="bg-white dark:bg-gray-800 shadow-lg p-4 flex flex-col h-screen sticky top-0 transition-all duration-300">
    <!-- Logo -->
    <div class="h-20 flex items-center justify-center">
        <a href="{{ route('aluno') }}" style="display: none;" x-show="true">
            <img src="{{ asset('images/unicentroma-logo.png') }}" alt="Unicentro"
                 class="object-contain transition-all duration-300"
                 :class="sidebarOpen ? 'h-14' : 'h-10'">
        </a>
    </div>

    <!-- Navegação -->
    <nav class="flex-1">
        <ul class="space-y-2">
            <li>
                <a href="{{ route('aluno') }}" title="Início" class="flex items-center gap-3 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700 px-3 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('aluno') ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400' : '' }}" :class="{'justify-center': !sidebarOpen}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="font-medium whitespace-nowrap" x-show="sidebarOpen" x-cloak>Início</span>
                </a>
            </li>
            <li>
                <a href="{{ route('favorites.index') }}" title="Favoritos" class="flex items-center gap-3 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700 px-3 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('favorites.index') ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400' : '' }}" :class="{'justify-center': !sidebarOpen}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    <span class="font-medium whitespace-nowrap" x-show="sidebarOpen" x-cloak>Favoritos</span>
                </a>
            </li>
            <li>
                <button @click.prevent="showSubmissions = true" title="Meus Envios" class="w-full flex items-center gap-3 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700 px-3 py-3 rounded-lg transition-all duration-200 text-left" :class="{'justify-center': !sidebarOpen}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    <span class="font-medium whitespace-nowrap" x-show="sidebarOpen" x-cloak>Meus envios</span>
                </button>
            </li>
        </ul>
    </nav>

    <!-- Ações do Usuário -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
        <a href="{{ route('profile.edit') }}" title="Perfil" class="flex items-center gap-3 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700 px-3 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('profile.*') ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400' : '' }}" :class="{'justify-center': !sidebarOpen}">
            <img class="w-8 h-8 rounded-full object-cover flex-shrink-0" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random" alt="">
            <div class="flex-1" x-show="sidebarOpen" x-cloak>
                <p class="font-medium text-sm truncate">{{ Auth::user()->name }}</p>
            </div>
        </a>
        <div class="flex items-center gap-1" :class="sidebarOpen ? '' : 'flex-col'">
            <button @click="toggleTheme()" title="Mudar Tema" class="flex-1 w-full flex items-center justify-center text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700 px-3 py-3 rounded-lg transition-all duration-200">
                <svg x-show="!darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                <svg x-show="darkMode" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </button>
            <form method="POST" action="{{ route('logout') }}" class="flex-1 w-full">
                @csrf
                <button type="submit" title="Sair" class="w-full flex items-center justify-center text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-700 px-3 py-3 rounded-lg transition-all duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H9m6 0v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v6z"></path></svg>
                </button>
            </form>
        </div>
        <div class="border-t border-gray-200 dark:border-gray-700 pt-2">
             <button @click="sidebarOpen = !sidebarOpen" title="Recolher/Expandir" class="w-full flex items-center justify-center text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700 px-3 py-3 rounded-lg transition-all duration-200">
                <svg x-show="sidebarOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                <svg x-show="!sidebarOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </div>
</div>
