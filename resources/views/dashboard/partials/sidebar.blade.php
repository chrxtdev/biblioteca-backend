<!-- Sidebar Simplificada Anti-Flicker -->
<aside 
    class="bg-white dark:bg-gray-800 shadow-xl flex flex-col flex-shrink-0 h-screen sticky top-0 z-30"
    :class="{ 
        'w-64': sidebarOpen, 
        'w-16': !sidebarOpen
    }"
    x-init="$nextTick(() => { isLoaded = true })"
    :style="isLoaded ? 'transition: width 300ms ease-in-out' : ''"
>
    <!-- Logo + Toggle (Header com Gradiente) -->
    <div class="flex items-center h-16 px-3 border-b border-teal-600 flex-shrink-0 bg-gradient-to-r from-green-500 to-teal-600">
        <!-- Logo (só quando expandido) -->
        <a 
            href="{{ route('aluno') }}" 
            class="overflow-hidden flex-1"
            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'"
            :style="isLoaded ? 'transition: opacity 200ms, width 200ms' : ''"
        >
            <img src="{{ asset('images/unicentro-logo-new.png') }}" alt="Unicentro" class="h-12 w-auto object-contain" />
        </a>

        <!-- Botão Toggle -->
        <button 
            @click="sidebarOpen = !sidebarOpen" 
            title="Recolher/Expandir"
            class="p-2 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-indigo-400 dark:hover:bg-gray-700 transition-colors flex-shrink-0"
        >
            <svg 
                class="w-5 h-5"
                :class="{ 'rotate-180': !sidebarOpen }"
                :style="isLoaded ? 'transition: transform 300ms' : ''"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
            </svg>
        </button>
    </div>

    <!-- Navegação -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-4">
        <ul class="space-y-1 px-2">
            <li>
                <a href="{{ route('aluno') }}" 
                   title="Início" 
                   class="flex items-center gap-3 py-2.5 px-3 rounded-lg {{ request()->routeIs('aluno') ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                   :class="{ 'justify-center': !sidebarOpen }"
                >
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span 
                        class="font-medium whitespace-nowrap overflow-hidden"
                        :class="sidebarOpen ? 'opacity-100 w-auto' : 'opacity-0 w-0'"
                        :style="isLoaded ? 'transition: opacity 200ms, width 200ms' : ''"
                    >Início</span>
                </a>
            </li>
            <li>
                <a href="{{ route('favorites.index') }}" 
                   title="Favoritos" 
                   class="flex items-center gap-3 py-2.5 px-3 rounded-lg {{ request()->routeIs('favorites.index') ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                   :class="{ 'justify-center': !sidebarOpen }"
                >
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <span 
                        class="font-medium whitespace-nowrap overflow-hidden"
                        :class="sidebarOpen ? 'opacity-100 w-auto' : 'opacity-0 w-0'"
                        :style="isLoaded ? 'transition: opacity 200ms, width 200ms' : ''"
                    >Favoritos</span>
                </a>
            </li>
            <li>
                <button 
                    @click.prevent="showSubmissions = true" 
                    title="Meus Envios" 
                    class="w-full flex items-center gap-3 py-2.5 px-3 rounded-lg text-left text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                    :class="{ 'justify-center': !sidebarOpen }"
                >
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                    <span 
                        class="font-medium whitespace-nowrap overflow-hidden"
                        :class="sidebarOpen ? 'opacity-100 w-auto' : 'opacity-0 w-0'"
                        :style="isLoaded ? 'transition: opacity 200ms, width 200ms' : ''"
                    >Meus envios</span>
                </button>
            </li>
        </ul>
    </nav>

    <!-- Rodapé -->
    <div class="flex-shrink-0 border-t border-gray-200 dark:border-gray-700 p-3 space-y-3">
        <!-- Perfil -->
        <a href="{{ route('profile.edit') }}" title="Perfil" class="flex items-center gap-3" :class="{ 'justify-center': !sidebarOpen }">
            <img class="w-9 h-9 rounded-full object-cover flex-shrink-0" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random&color=fff" alt="Avatar">
            <div 
                class="flex-1 overflow-hidden"
                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 hidden'"
                :style="isLoaded ? 'transition: opacity 200ms' : ''"
            >
                <p class="font-semibold text-sm text-gray-800 dark:text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Ver Perfil</p>
            </div>
        </a>

        <!-- Botões -->
        <div class="flex gap-2" :class="sidebarOpen ? 'flex-row' : 'flex-col'">
            <button 
                @click="toggleTheme()" 
                title="Mudar Tema" 
                class="flex-1 flex items-center justify-center p-2 rounded-lg bg-gray-100 dark:bg-gray-700/50 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors"
            >
                <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
                <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </button>

            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                @csrf
                <button 
                    type="submit" 
                    title="Sair" 
                    class="w-full flex items-center justify-center p-2 rounded-lg bg-gray-100 dark:bg-gray-700/50 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-500 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
