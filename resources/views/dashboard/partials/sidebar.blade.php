<!-- Sidebar Anti-Flicker (Pure CSS initial state) -->
<aside 
    id="app-sidebar"
    class="bg-white dark:bg-gray-800 shadow-xl flex flex-col flex-shrink-0 h-screen sticky top-0 z-30"
    :style="{ width: sidebarOpen ? '16rem' : '5rem' }"
>
    <!-- Logo + Toggle (Header com Gradiente) -->
    <div class="sb-header flex items-center h-16 px-0 border-b border-teal-600 flex-shrink-0 bg-gradient-to-r from-green-500 to-teal-600 overflow-hidden relative justify-between"
         :style="{ justifyContent: sidebarOpen ? 'space-between' : 'center' }">
        
        <!-- Logo (hidden via CSS when sidebar-closed) -->
        <a href="{{ route('aluno') }}" 
           class="sb-expanded flex-1 flex items-center justify-start pl-4"
           :style="{ display: sidebarOpen ? 'flex' : 'none' }"
        >
            <img src="{{ asset('images/unicentro-logo-new.png') }}" alt="Unicentro" class="h-10 w-auto object-contain brightness-0 invert" />
        </a>

        <!-- Botão Toggle -->
        <button 
            @click="sidebarOpen = !sidebarOpen" 
            title="Recolher/Expandir"
            class="sb-toggle p-2 rounded-lg text-white/80 hover:text-white hover:bg-white/10 absolute right-3"
            :style="{ position: sidebarOpen ? 'absolute' : 'static' }"
        >
            <svg 
                class="w-6 h-6"
                :class="{ 'rotate-180': !sidebarOpen }"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
            </svg>
        </button>
    </div>

    <!-- Navegação -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-4 custom-scrollbar">
        <ul class="space-y-2 px-3">
            <li>
                <a href="{{ route('aluno') }}" 
                   title="Início" 
                   class="sb-link flex items-center gap-3 py-3 px-3 rounded-xl group {{ request()->routeIs('aluno') ? 'bg-teal-50 dark:bg-teal-900/20 text-teal-600 dark:text-teal-400 font-semibold shadow-sm ring-1 ring-teal-500/10' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200' }}"
                   :style="{ justifyContent: sidebarOpen ? '' : 'center', gap: sidebarOpen ? '' : '0' }"
                >
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <span class="sb-expanded whitespace-nowrap"
                          :style="{ display: sidebarOpen ? '' : 'none' }"
                    >Início</span>
                </a>
            </li>
            <li>
                <a href="{{ route('favorites.index') }}" 
                   title="Favoritos" 
                   class="sb-link flex items-center gap-3 py-3 px-3 rounded-xl group {{ request()->routeIs('favorites.index') ? 'bg-teal-50 dark:bg-teal-900/20 text-teal-600 dark:text-teal-400 font-semibold shadow-sm ring-1 ring-teal-500/10' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200' }}"
                   :style="{ justifyContent: sidebarOpen ? '' : 'center', gap: sidebarOpen ? '' : '0' }"
                >
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <span class="sb-expanded whitespace-nowrap"
                          :style="{ display: sidebarOpen ? '' : 'none' }"
                    >Favoritos</span>
                </a>
            </li>
            <li>
                <button 
                    @click.prevent="showSubmissions = true" 
                    title="Meus Envios" 
                    class="sb-link w-full flex items-center gap-3 py-3 px-3 rounded-xl text-left group text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-gray-200"
                    :style="{ justifyContent: sidebarOpen ? '' : 'center', gap: sidebarOpen ? '' : '0' }"
                >
                    <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                    </div>
                    <span class="sb-expanded whitespace-nowrap"
                          :style="{ display: sidebarOpen ? '' : 'none' }"
                    >Meus envios</span>
                </button>
            </li>
        </ul>
    </nav>

    <!-- Rodapé -->
    <div class="flex-shrink-0 border-t border-gray-100 dark:border-gray-700/50 p-3 space-y-3 bg-white dark:bg-gray-800">
        <!-- Perfil -->
        <a href="{{ route('profile.edit') }}" title="Perfil" class="sb-link flex items-center gap-3 group"
           :style="{ justifyContent: sidebarOpen ? '' : 'center', gap: sidebarOpen ? '' : '0' }">
            <div class="relative flex-shrink-0">
                <img class="w-10 h-10 rounded-full object-cover ring-2 ring-white dark:ring-gray-700 shadow-sm" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=14b8a6&color=fff" alt="Avatar">
                <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white dark:ring-gray-800 bg-green-400"></span>
            </div>
            <div class="sb-expanded flex-1 overflow-hidden"
                 :style="{ display: sidebarOpen ? '' : 'none' }">
                <p class="font-bold text-sm text-gray-800 dark:text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Ver Perfil</p>
            </div>
        </a>

        <!-- Botões -->
        <div class="sb-footer-btns flex gap-2 flex-row"
             :style="{ flexDirection: sidebarOpen ? 'row' : 'column', alignItems: sidebarOpen ? '' : 'center' }">
            <button 
                @click="toggleTheme()" 
                title="Mudar Tema" 
                class="sb-btn flex-1 flex items-center justify-center p-2.5 rounded-xl bg-gray-50 dark:bg-gray-700/30 text-gray-500 hover:text-teal-600 hover:bg-teal-50 dark:text-gray-400 dark:hover:text-teal-400 dark:hover:bg-teal-900/20"
                :style="sidebarOpen ? '' : 'width:2.5rem;height:2.5rem;flex:none'"
            >
                <svg class="icon-light w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
                <svg class="icon-dark w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </button>

            <form method="POST" action="{{ route('logout') }}" class="sb-btn-form flex-1"
                  :style="sidebarOpen ? '' : 'flex:none'">
                @csrf
                <button 
                    type="submit" 
                    title="Sair" 
                    class="sb-btn w-full flex items-center justify-center p-2.5 rounded-xl bg-gray-50 dark:bg-gray-700/30 text-gray-500 hover:text-red-600 hover:bg-red-50 dark:text-gray-400 dark:hover:text-red-400 dark:hover:bg-red-900/20"
                    :style="sidebarOpen ? '' : 'width:2.5rem;height:2.5rem;flex:none'"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
