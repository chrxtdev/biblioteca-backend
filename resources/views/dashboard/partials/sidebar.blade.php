<!-- Sidebar -->
<div class="w-64 bg-white dark:bg-gray-800 shadow-lg p-6 flex flex-col h-screen sticky top-0">
    <!-- Logo -->
    <div class="mb-8">
        <img src="{{ asset('images/unicentroma-logo.png') }}" alt="Unicentro" class="h-10 w-auto object-contain">
    </div>

    <!-- Navegação -->
    <nav class="flex-1">
        <ul class="space-y-2">
            <li>
                <a href="{{ route('aluno') }}"
                    class="flex items-center gap-3 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('aluno') ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span class="font-medium">Ínicio</span>
                </a>
            </li>

            <li>
                <button @click="showSubmissions = true"
                    class="w-full flex items-center gap-3 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700 px-4 py-3 rounded-lg transition-all duration-200 text-left">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4">
                        </path>
                    </svg>
                    <span class="font-medium">Meus envios</span>
                    @if(isset($myBooks) && !$myBooks->isEmpty())
                        <span
                            class="ml-auto bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs px-2 py-1 rounded-full font-bold">
                            {{ $myBooks->count() }}
                        </span>
                    @endif
                </button>
            </li>
            
            <li>
                <a href="{{ route('aluno') }}"
                    class="flex items-center gap-3 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700 px-4 py-3 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.536 8.464a5 5 0 010 7.072m2.828-9.464a5 5 0 00-7.072 2.828A5 5 0 0112 19.928V15a3 3 0 00-3-3m9-9a3 3 0 00-3 3v4.928">
                        </path>
                    </svg>
                    <span class="font-medium">Favoritos</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Ações do Usuário -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
        <!-- Perfil -->
        <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-3 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('profile.*') ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400' : '' }}">
            <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center">
                <span
                    class="text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ substr(Auth::user()->name, 0, 2) }}</span>
            </div>
            <div class="flex-1">
                <p class="font-medium text-sm">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Configurações</p>
            </div>
        </a>

        <!-- Modo Escuro e Configurações -->
        <div class="flex items-center gap-1">
            <!-- Modo Escuro -->
            <button @click="toggleTheme()"
                class="flex-1 flex items-center justify-center text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-700 px-4 py-3 rounded-lg transition-all duration-200">
                <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                    </path>
                </svg>
                <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
            </button>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-700 px-4 py-3 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H9m6 0v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v6z">
                        </path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>